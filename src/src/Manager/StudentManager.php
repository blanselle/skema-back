<?php

declare(strict_types=1);

namespace App\Manager;

use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Entity\Country;
use App\Entity\CV\BacSup;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\User\StudentWorkflowHistory;
use App\Exception\ResetDbException;
use App\Helper\DbHelper;
use Doctrine\Common\Collections\Collection;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use \DateTime;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StudentManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private DbHelper $dbHelper,
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
        private AdmissibilityStudentTokenRepository $admissibilityStudentTokenRepository,
    ) {
    }

    public function getStudentLastDiploma(Student $student): ?StudentDiploma
    {
        if (null === $student->getAdministrativeRecord()) {
            return null;
        }

        $studentDiplomas = $student->getAdministrativeRecord()->getStudentDiplomas();
        if ($studentDiplomas->isEmpty()) {
            return null;
        }

        foreach ($student->getAdministrativeRecord()->getStudentDiplomas() as $diploma) {
            if (true === $diploma->getLastDiploma()) {
                return $diploma;
            }
        }

        return null;
    }

    public function wentThroughSate(Student $student, array $states): bool
    {
        $history = $this->em->getRepository(StudentWorkflowHistory::class)->findBy(
            [
                'student' => $student,
                'state' => $states
            ]
        );

        return !empty($history);
    }

    public function hasAlreadyApplyToSessionTheSameDayOnOtherCampus(Student $student, ExamSession $examSession): bool
    {
        $examStudents = $this->em->getRepository(ExamStudent::class)->findByStudentAvoidingCollisionOnOtherCampus(
            $student,
            $examSession
        );

        return !empty($examStudents);
    }

    public function hasAlreadyApplyToSessionTheSameDayOnSameSessionType(Student $student, ExamSession $examSession, bool $existing): bool
    {
        $examStudents = $this->em->getRepository(ExamStudent::class)->findByStudentAvoidingCollisionOnSameSessionType(
            $student,
            $examSession
        );

        if (!$existing) {
            return !empty($examStudents);
        }

        return count($examStudents) > 1;
    }

    public function hasAlreadyApplyToSameSession(Student $student, ExamSession $examSession, bool $existing): bool
    {
        $examStudents = $this->em->getRepository(ExamStudent::class)->findBy(
            [
                'student' => $student,
                'examSession' => $examSession
            ]
        );

        if (!$existing) {
            return !empty($examStudents);
        }

        return count($examStudents) > 1;
    }

    public function hasDualPathStudentDiploma(Student $student): bool
    {
        $medias = $this->em->getRepository(Media::class)->findBy([
            'student' => $student,
            'state' => MediaWorflowStateConstants::STATE_ACCEPTED,
            'code' => MediaCodeConstants::CODE_CERTIFICAT_DOUBLE_PARCOURS
        ]);

        return 0 < count($medias);
    }

    public function getSummons(Student $student): Collection
    {
        return $student->getExamSummons();
    }

    public function studentHasPayedOrIsScholarShip(Student $student): bool
    {
        if($student->getAdministrativeRecord()->getScholarShip() === true) {
            return true;
        }

        $order = $student->getOrder(type: OrderTypeConstants::SCHOOL_REGISTRATION_FEES);

        if(OrderWorkflowStateConstants::STATE_VALIDATED === $order?->getState()) {
            return true;
        }

        return false;
    }

    /**
     * @throws ResetDbException
     * 
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function anonymize(Student $student): void
    {
        $this->dbHelper->deleteStudentMediasPhysically($student->getId());

        $administrativeRecord = $student->getAdministrativeRecord();
        $cv = $student->getCv();

        // set dualPath to null in order to delete them
        $studentDiplomas = $administrativeRecord->getStudentDiplomas();
        /** @var StudentDiploma $studentDiploma */
        foreach ($studentDiplomas as $studentDiploma) {
            $studentDiploma->setDualPathStudentDiploma(null);
        }
        $this->em->flush();

        // delete all student's medias
        $studentMedias = $this->em->getRepository(Media::class)->findBy(['student' => $student]);
        foreach ($studentMedias as $media) {
            $mediaPath = sprintf('%s/%s', $this->projectDir, $media->getFile());
            if (file_exists($mediaPath)) {
                @unlink($mediaPath);
            }
            $this->em->remove($media);
        }

        $this->em->remove($administrativeRecord);
        foreach ($cv->getBacSups() as $bacSup) {
            /** @var BacSup $bacSup */
            if (null === $bacSup->getParent()) {
                continue;
            }
            $bacSup->getParent()->setDualPathBacSup(null);
            $this->em->remove($bacSup->getParent());
        }
        $this->em->remove($cv);

        $studentOrders = $student->getOrders();
        foreach ($studentOrders as $studentOrder) {
            if(null != $studentOrder->getExamSession()){
                $studentOrder->setExamSession(null);
            }
        }

        // delete examStudents and associated examSession if needed
        $examStudents = $student->getExamStudents();
        foreach ($examStudents as $examStudent) {
            $examStudent->setMedia(null);

            $this->em->remove($examStudent);
            if (1 === $examStudent->getExamSession()->getExamStudents()->count()) {
                $this->em->remove($examStudent->getExamSession());
            }
        }

        $this->em->flush();

        $admissibilityStudentTokens = $this->admissibilityStudentTokenRepository->findBy(['student' => $student]);
        foreach($admissibilityStudentTokens as $token) {
            $this->em->remove($token);
        }

        $student->setAddress('Anonymized');
        $student->setCity('Anonymized');
        $student->setPostalCode('Anonymized');
        $student->setDateOfBirth(new DateTime('1970-01-01'));
        $student->setPhone('Anonymized');
        $student->setNationalitySecondary(null);
        $student->setFirstNameSecondary('Anonymized');
        $student->setNationality($this->em->getRepository(Country::class)->findOneBy(['idCountry' => 'FRA']));
        $student->setCountry($this->em->getRepository(Country::class)->findOneBy(['idCountry' => 'FRA']));
        $student->setCountryBirth($this->em->getRepository(Country::class)->findOneBy(['idCountry' => 'FRA']));
        $student->setAdmissibilityGlobalNote(null);
        $student->setAdmissibilityRanking(null);
        $student->setAdmissibilityMaxScore(null);
        $student->setEnglishNoteUsed(null);
        $student->setManagementNoteUsed(null);
        $student->setAdmissibilityMaxScore(null);

        $student->getUser()->setFirstName('Anonymized');
        $student->getUser()->setLastName('Anonymized');
        $student->getUser()->setPassword('Anonymized');
        $student->getUser()->setEmail(sprintf('anonymous_%s@skema.fr', $student->getIdentifier()));

        $student->setAdministrativeRecord(null);
        $student->setCv(null);
        $this->em->flush();
        $this->em->clear();

        $student = $this->em->getRepository(Student::class)->find($student->getId());

        $histories = $this->em->getRepository(History::class)->findBy(['student' => $student]);
        foreach ($histories as $history) {
            $this->em->remove($history);
        }
        $this->em->flush();

        $student->setAnonymized(true);
        $this->em->flush();
    }
}
