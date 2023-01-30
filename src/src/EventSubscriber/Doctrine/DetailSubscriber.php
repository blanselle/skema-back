<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine;

use App\Interface\DetailInterface;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DetailSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
            Events::preUpdate => 'preUpdate',
        ];
    }

    public function __construct(private ValidatorInterface $validator) {}

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof DetailInterface) {
            return;
        }

        $this->setDetailToNull($entity);
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof DetailInterface) {
            return;
        }

        $this->setDetailToNull($entity);
    }

    private function setDetailToNull(DetailInterface $object): void 
    {
        if(count($this->validator->validateProperty($object, 'detail', groups: ['detail-to-null'])) > 0){
            $object->setDetail(null);
        }
    }
}
