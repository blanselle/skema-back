<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\OralTest;

use ApiPlatform\HttpCache\VarnishPurger;
use App\Constants\OralTest\OralTestStudentWorkflowStateConstants;
use App\Entity\OralTest\OralTestStudent;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class OnOralTestStudentValidationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private VarnishPurger $purger,
        private StudentWorkflowManager $studentWorkflowManager,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.oral_test_student.entered.%s',
                OralTestStudentWorkflowStateConstants::VALIDATED
            ) => [
                    ['increaseReservedPlaces'],
                    ['invalidCache'],
                    ['updateStudentWorkflow']
            ],
            sprintf(
                'workflow.oral_test_student.entered.%s',
                OralTestStudentWorkflowStateConstants::REJECTED
            ) => 'invalidCache',
        ];
    }

    public function increaseReservedPlaces(Event $event): void
    {
        $oralTestStudent = $event->getSubject();

        if (!$oralTestStudent instanceof OralTestStudent) {
            return;
        }

        $oralTestStudent->getCampusOralDay()->setNbOfReservedPlaces(
            $oralTestStudent->getCampusOralDay()->getNbOfReservedPlaces() + 1
        );

        $this->em->flush();
    }

    public function invalidCache(Event $event): void
    {
        $oralTestStudent = $event->getSubject();

        if (!$oralTestStudent instanceof OralTestStudent) {
            return;
        }

        $this->purger->purge([sprintf('/api/oral_test_students/%d/check', $oralTestStudent->getId())]);
    }

    public function updateStudentWorkflow(Event $event): void
    {
        $oralTestStudent = $event->getSubject();

        if (!$oralTestStudent instanceof OralTestStudent) {
            return;
        }

        $student = $oralTestStudent->getStudent();
        $this->studentWorkflowManager->admissibleToRegisteredEo($student);
    }
}
