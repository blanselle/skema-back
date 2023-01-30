<?php

declare(strict_types=1);

namespace App\Service\Exam;

use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class DistributionManager
{
    private array $examStudents;

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function makeDistribution(int $campusId): void
    {
        $exams = $this->em->getRepository(ExamSession::class)->findBy(['campus' => $campusId, 'type' => ExamSessionTypeConstants::TYPE_INSIDE]);
        try {
            /** @var ExamSession $exam */
            foreach ($exams as $exam) {
                $this->em->getRepository(ExamStudent::class)->updateExamStudentRoom($exam->getId());
                $this->distributionForThirdTimes($exam);
                $this->distributionForOthers($exam);
                $exam->setDistributed(true);
            }
        } catch (Exception $e) {
            throw $e;
        }
        $this->em->flush();
    }

    private function distributionForThirdTimes(ExamSession $exam): void
    {
        $this->examStudents = $this->em->getRepository(ExamStudent::class)->getExamStudentsByExamSession($exam, true);

        $nbStudents = count($this->examStudents);
        if ($nbStudents > 0) {
            if (!$this->checkNbOfPlacesAvailable($exam, $nbStudents, true)) {
                throw new Exception(
                    sprintf(
                        "Merci de paramétrer une salle « tiers temps » sur le campus %s pour la session %s du %s",
                        $exam->getCampus()->getName(),
                        $exam->getExamClassification()->getName(),
                        $exam->getDateStart()->format('Y-m-d H:i')
                    )
                );
            }
            $this->distribute($exam, $this->examStudents, true);
        }
    }

    private function distributionForOthers(ExamSession $exam): void
    {
        $this->examStudents = $this->em->getRepository(ExamStudent::class)->getExamStudentsByExamSession($exam);
        
        $nbStudents = count($this->examStudents);
        if ($nbStudents > 0) {
            if (!$this->checkNbOfPlacesAvailable($exam, $nbStudents, false)) {
                throw new Exception(
                    sprintf(
                        "Merci de paramétrer une salle sur le campus %s pour la session %s.",
                        $exam->getCampus()->getName(),
                        $exam->getExamClassification()->getName()
                    )
                );
            }
            $this->distribute($exam, $this->examStudents);
        }
    }

    private function checkNbOfPlacesAvailable(ExamSession $examSession, int $nbStudent, bool $thirdTime): bool
    {
        $nbPlaces = $this->em->getRepository(ExamSession::class)->getNbTotalPlaces($examSession->getId(), $thirdTime);
        if ($nbStudent > $nbPlaces) {
            return false;
        }

        return true;
    }

    private function distribute(ExamSession $exam, array $examStudents, bool $thirdTime = false): void
    {   
        foreach ($examStudents as $examStudent) {
            /** @var ExamStudent $examStudent */
            if (true == $this->isThirdTimeSpecific($examStudent->getStudent())) {
                $roomId = $this->em->getRepository(ExamStudent::class)->getAvailableRoomByIdWithMinStudents($exam->getId(), true);
                if (is_numeric($roomId) && $roomId > 0) {
                    $this->em->getRepository(ExamStudent::class)->updateExamStudent($exam->getId(), $examStudent->getStudent()->getId(), $roomId, true);
                }
            } else {
                $roomId = $this->em->getRepository(ExamStudent::class)->getAvailableRoomByIdWithMinStudents($exam->getId(), $thirdTime);
                if (is_numeric($roomId) && $roomId > 0) {
                    $this->em->getRepository(ExamStudent::class)->updateExamStudent($exam->getId(), $examStudent->getStudent()->getId(), $roomId, false);
                }
            }
        }
    }

    private function isThirdTimeSpecific(Student $student): bool
    {
        if (!empty($student->getAdministrativeRecord()) &&
            true == $student->getAdministrativeRecord()->getThirdTime() &&
            true == $student->getAdministrativeRecord()->getThirdTimeNeedDetail()) {
            return true;
        }

        return false;
    }
}
