<?php

namespace App\EventSubscriber\Doctrine\Student;

use App\Entity\Student;
use App\Service\Workflow\ProgramChannelSwitchManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

class StudentSubscriber implements EventSubscriberInterface
{
    private ?Student $student = null;

    public function __construct(private ProgramChannelSwitchManager $switchManager)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        /** @var Student $entity */
        foreach($args->getObjectManager()->getUnitOfWork()->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof Student && null !== $entity->getProgramChannel()
                && isset($args->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity)['programChannel'])) {
                $this->switchManager->updateProgramChannelVerification($entity, $entity->getProgramChannel());
                $this->student = $entity;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if (null !== $this->student) {
            $this->switchManager->dispatch(student: $this->student, newProgramChannel: $this->student->getProgramChannel());
        }
    }
}