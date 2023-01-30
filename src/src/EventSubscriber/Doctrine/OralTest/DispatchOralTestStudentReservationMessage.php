<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\OralTest;

use App\Entity\OralTest\OralTestStudent;
use App\Message\OralTestStudentReservation;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

class DispatchOralTestStudentReservationMessage implements EventSubscriberInterface
{
    private array $oralTestStudents = [];

    public function __construct(
        private MessageBusInterface $bus,
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postFlush,
            Events::onFlush,
        ];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        foreach($args->getObjectManager()->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof OralTestStudent) {
                $this->oralTestStudents[] = $entity;

                return;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        /** @var OralTestStudent $oralTestStudent */
        foreach ($this->oralTestStudents as $oralTestStudent) {
            $this->bus->dispatch(new OralTestStudentReservation($oralTestStudent->getId()));
        }
    }
}
