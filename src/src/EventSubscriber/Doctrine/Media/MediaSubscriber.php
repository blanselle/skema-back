<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\Media;

use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaTypeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Media;
use App\Service\Media\MediaUploader;
use App\Service\Workflow\Media\MediaWorkflowManager;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MediaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MediaUploader $mediaUploader,
        private MediaWorkflowManager $mediaWorkflowManager,
        private StudentWorkflowManager $studentWorkflowManager,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::postUpdate,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Media) {
            return;
        }

        $type = match ($entity->getCode()) {
            MediaCodeConstants::CODE_AUTRE => MediaTypeConstants::TYPE_IMAGE_CMS,
            MediaCodeConstants::CODE_ID_CARD, MediaCodeConstants::CODE_JOURNEE_DEFENSE_CITOYENNE, MediaCodeConstants::CODE_SUMMON => MediaTypeConstants::TYPE_DOCUMENT_SIMPLE,
            default => MediaTypeConstants::TYPE_DOCUMENT_TO_VALIDATE
        };
        $entity->setType($type);

        if ($entity->getType() === MediaTypeConstants::TYPE_DOCUMENT_TO_VALIDATE) {
            $this->mediaWorkflowManager->uploadedToCheck($entity);
            if (!empty($entity->getStudent())) {
                $this->studentWorkflowManager->recheckBoursier($entity->getStudent());
            }
        }

        $this->mediaUploader->upload($entity);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Media) {
            return;
        }

        if ($entity->getState() === MediaWorflowStateConstants::STATE_ACCEPTED) {
            $this->studentWorkflowManager->valid($entity->getStudent());
            $this->studentWorkflowManager->eligible($entity->getStudent());
        }

        $this->mediaUploader->upload($entity);
    }
}
