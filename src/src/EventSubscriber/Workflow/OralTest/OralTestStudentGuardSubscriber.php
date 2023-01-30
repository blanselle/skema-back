<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\OralTest;

use App\Constants\OralTest\OralTestStudentWorkflowTransitionConstants;
use App\Entity\OralTest\OralTestStudent;
use App\Service\OralTest\CampusOralDayManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class OralTestStudentGuardSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CampusOralDayManager $campusOralDayManager,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.oral_test_student.guard.%s',
                OralTestStudentWorkflowTransitionConstants::VALIDATE
            ) => 'waitingForTreatmentToValidate',
            sprintf(
                'workflow.oral_test_student.guard.%s',
                OralTestStudentWorkflowTransitionConstants::REJECT
            ) => 'waitingForTreatmentToReject',
        ];
    }

    public function waitingForTreatmentToValidate(GuardEvent $event): void
    {
        $oralTestStudent = $event->getSubject();

        if (!$oralTestStudent instanceof OralTestStudent) {
            return;
        }

        if ($this->campusOralDayManager->canBeReserved($oralTestStudent->getCampusOralDay())) {
            return;
        }

        $event->setBlocked(true);
    }

    public function waitingForTreatmentToReject(GuardEvent $event): void
    {
        $oralTestStudent = $event->getSubject();

        if (!$oralTestStudent instanceof OralTestStudent) {
            return;
        }

        if (!$this->campusOralDayManager->canBeReserved($oralTestStudent->getCampusOralDay())) {
            return;
        }

        $event->setBlocked(true);
    }
}
