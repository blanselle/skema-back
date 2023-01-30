<?php

declare(strict_types=1);

namespace App\Service\Workflow\Media;

use App\Constants\Media\MediaWorkflowTransitionConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\StudentWorkflowTransitionConstants;
use App\Entity\Media;
use App\Service\CandidateManager;
use Symfony\Component\Workflow\Registry;

class MediaWorkflowManager
{
    public function __construct(
        private Registry $workflowRegistry,
        private CandidateManager $candidateManager
    ) {
    }

    public function uploadedToCheck(Media $media): void
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::UPLOADED_TO_CHECK)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::UPLOADED_TO_CHECK);

            // Spec 15.710
            $student = $media->getStudent();
            if (null !== $student && $student->getState() === StudentWorkflowStateConstants::STATE_COMPLETE_PROOF) {
                if (true === $this->candidateManager->hasAllDocumentsMandatoryToComplete($student)) {
                    $workflow = $this->workflowRegistry->get($media, 'candidate');
                    if ($workflow->can($student, StudentWorkflowTransitionConstants::TO_COMPLETE)) {
                        $workflow->apply($student, StudentWorkflowTransitionConstants::TO_COMPLETE);
                    }
                }
            }
        }
    }

    public function checkToAccepted(Media $media): void
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::CHECK_TO_ACCEPTED)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::CHECK_TO_ACCEPTED);
        }
    }

    public function toCheckToRejected(Media $media): void
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::CHECK_TO_REJECTED)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::CHECK_TO_REJECTED);
        }
    }

    public function checkToTransfered(Media $media): void
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::CHECK_TO_TRANSFERED)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::CHECK_TO_TRANSFERED);
        }
    }

    public function transferedToAccepted(Media $media): void
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::TRANSFERED_TO_ACCEPTED)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::TRANSFERED_TO_ACCEPTED);
        }
    }

    public function transferedToRejected(Media $media): void
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::TRANSFERED_TO_REJECTED)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::TRANSFERED_TO_REJECTED);
        }
    }

    public function acceptedToRejected(Media $media): bool
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::ACCEPTED_TO_REJECTED)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::ACCEPTED_TO_REJECTED);
            return false;
        }
        return true;
    }

    public function acceptedToCheck(Media $media): bool
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::ACCEPTED_TO_CHECK)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::ACCEPTED_TO_CHECK);
            return false;
        }
        return true;
    }

    public function toCancel(Media $media): bool
    {
        $workflow = $this->workflowRegistry->get($media, 'media');

        if ($workflow->can($media, MediaWorkflowTransitionConstants::TO_CANCEL)) {
            $workflow->apply($media, MediaWorkflowTransitionConstants::TO_CANCEL);
            return true;
        }
        
        return false;
    }
}
