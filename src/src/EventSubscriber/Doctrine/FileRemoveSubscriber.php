<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine;

use App\Interface\FileInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class FileRemoveSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::postRemove => 'postRemove',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof FileInterface) {
            return;
        }

        if (file_exists($entity->getFilePath())) {
            unlink($entity->getFilePath());
        }
    }
}
