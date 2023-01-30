<?php

declare(strict_types=1);

namespace App\Service\OralTest;

use App\Constants\OralTest\OralTestStudentWorkflowStateConstants;
use App\Entity\Campus;
use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\OralTestStudent;
use App\Entity\Student;
use App\Repository\OralTest\CampusOralDayRepository;
use App\Repository\OralTest\OralTestStudentRepository;
use App\Service\Workflow\OralTest\OralTestStudentWorkflowManager;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class OralTestManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private CampusOralDayRepository $campusOralDayRepository,
        private OralTestStudentWorkflowManager $oralTestStudentWorkflowManager,
        private OralTestStudentRepository $oralTestStudentRepository,
    ){}

    public function replaceOralTestStudent(
        Student $student,
        DateTimeInterface $date,
        Campus $campus,
        ExamLanguage $firstLanguage,
        ExamLanguage $secondLanguage,
    ): OralTestStudent {

        $oralTestStudent = $this->oralTestStudentRepository->findOneBy([
            'state' => OralTestStudentWorkflowStateConstants::VALIDATED,
            'student' => $student,
        ]);
        if($oralTestStudent !== null){
            $this->em->remove($oralTestStudent);
            $this->em->flush();
        }

        $oralTestStudent = (new OralTestStudent())
            ->setStudent($student)
        ;

        $campusOralDay = $this->campusOralDayRepository->findOneSlot(
            campus: $campus,
            programChannel: $student->getProgramChannel(),
            start: $date,
            firstLanguage: $firstLanguage,
            secondLanguage: $secondLanguage,
        );
        
        if(null === $campusOralDay) {
            throw new Exception('Slot not found');
        }
        
        $oralTestStudent->setCampusOralDay($campusOralDay);

        $this->em->persist($oralTestStudent);

        $this->oralTestStudentWorkflowManager->validateForce($oralTestStudent);

        $this->em->flush();

        return $oralTestStudent;
    }
}
