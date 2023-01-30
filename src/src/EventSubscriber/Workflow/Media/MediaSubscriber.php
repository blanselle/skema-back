<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Media;

use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorkflowTransitionConstants;
use App\Entity\Media;
use App\Repository\StudentRepository;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;

class MediaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private StudentWorkflowManager $studentWorkflowManager,
        private StudentRepository $studentRepository
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.media.completed.%s',
                MediaWorkflowTransitionConstants::CHECK_TO_ACCEPTED
            ) => 'checkToAccepted',
            sprintf(
                'workflow.media.completed.%s',
                MediaWorkflowTransitionConstants::ACCEPTED_TO_REJECTED
            ) => 'refuseScholarship',
            sprintf(
                'workflow.media.completed.%s',
                MediaWorkflowTransitionConstants::CHECK_TO_REJECTED
            ) => 'refuseScholarship',
            sprintf(
                'workflow.media.completed.%s',
                MediaWorkflowTransitionConstants::TRANSFERED_TO_REJECTED
            ) => 'refuseScholarship',
        ];
    }

    public function refuseScholarship(CompletedEvent $event): void
    {
        $media = $event->getSubject();

        if (!$media instanceof Media) {
            return;
        }

        if($media->getCode() !== MediaCodeConstants::CODE_CROUS) {
            return;
        }

        if (null === $media->getStudent()) {
            return;
        }

        // Needed to get the current student
        $student = $this->studentRepository->findOneBy(['id' => $media->getStudent()->getId()]);

        $this->studentWorkflowManager->refuseScholarship($student);

    }

    public function checkToAccepted(CompletedEvent $event): void
    {
        $media = $event->getSubject();
        if (!$media instanceof Media) {
            return;
        }
        
        if($media->getCode() !== MediaCodeConstants::CODE_CROUS) {
            return;
        }
            
        if(null === $media->getStudent()) {
            return;
        }

        $this->studentWorkflowManager->valid($media->getStudent());
        $this->studentWorkflowManager->eligible($media->getStudent());
    }
}