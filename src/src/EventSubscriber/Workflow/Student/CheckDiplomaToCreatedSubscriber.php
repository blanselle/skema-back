<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Student;

use App\Constants\Notification\NotificationConstants;
use App\Constants\User\StudentWorkflowTransitionConstants;
use App\Entity\Student;
use App\Manager\NotificationManager;
use App\Service\Notification\NotificationCenter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class CheckDiplomaToCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationCenter $notificationCenter,
        private NotificationManager $notificationManager,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.candidate.transition.%s',
                StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_CREATED
            ) => 'checkDiplomaToCreated',
        ];
    }

    public function checkDiplomaToCreated(TransitionEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        $this->notificationCenter->dispatch(
            $this->notificationManager->createNotification(
                receiver: $student->getUser(),
                blocKey: 'NOTIFICATION_CHECK_DIPLOMA_TO_CREATED',
                params: ['firstname' => $student->getUser()->getFirstName()]
            ),
            [NotificationConstants::TRANSPORT_DB]
        );
    }
}
