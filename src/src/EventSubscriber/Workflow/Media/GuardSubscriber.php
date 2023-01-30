<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Media;

use App\Constants\Media\MediaWorkflowTransitionConstants;
use App\Entity\Media;
use App\Security\MediaVoter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Workflow\Event\GuardEvent;

class GuardSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.media.guard.%s',
                MediaWorkflowTransitionConstants::ACCEPTED_TO_REJECTED
            ) => 'acceptedToRejected',
        ];
    }

    public function acceptedToRejected(GuardEvent $event): void
    {
        $media = $event->getSubject();
        if (!$media instanceof Media) {
            return;
        }

        if($this->security->isGranted(MediaVoter::ACTION_REJECT, $media)) {
            return;
        }

        $event->setBlocked(true);
    }
}
