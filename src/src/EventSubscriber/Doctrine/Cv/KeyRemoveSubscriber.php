<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\Cv;

use App\Exception\Cv\KeyRemoveException;
use App\Interface\Cv\KeyInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class KeyRemoveSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove => 'preRemove',
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof KeyInterface) {
            return;
        }
        
        if($entity->getKey() !== null) {
            throw new KeyRemoveException('Unable to delete a entity with a key');
        }
    }
}
