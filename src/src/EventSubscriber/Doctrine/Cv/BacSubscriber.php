<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\Cv;

use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Entity\CV\Bac\Bac;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class BacSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MediaWorkflowManager $mediaWorkflowManager,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Bac) {
            return;
        }
        if (null != $entity->getMedia() && $entity->getMedia()->getCode() === MediaCodeConstants::CODE_BAC &&
            $entity->getBacDistinction()->getCode() === DistinctionCodeConstants::NO_DISTINCTION) {
            $this->mediaWorkflowManager->checkToAccepted($entity->getMedia());
        }
    }
}
