<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\Cv;

use App\Entity\CV\Cv;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Service\Admissibility\Cv\CvCalculationDispatcher;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;

class CvSubscriber implements EventSubscriberInterface
{
    private ?Cv $cv; 

    public function __construct(
        private CvCalculationDispatcher $cvCalculationDispatcher,
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
        foreach($args->getEntityManager()->getUnitOfWork()->getScheduledEntityUpdates() as $entity) {
            
            if(// Pour éviter la récursivité !
                $entity instanceof Cv && (
                    isset($args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity)['note']) or
                    isset($args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity)['bonus'])
                )
            ){
                return;
            }

            if($entity instanceof CvCalculationInterface) {
                if(null !== $entity->getCv()) {
                    $this->cv = $entity->getCv();
                }
                return;
            }
        }

        foreach($args->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof CvCalculationInterface) {
                if(null !== $entity->getCv()) {
                    $this->cv = $entity->getCv();
                }
                return;
            }
        }

        foreach($args->getEntityManager()->getUnitOfWork()->getScheduledEntityDeletions() as $entity) {
            if($entity instanceof CvCalculationInterface) {
                if(null !== $entity->getCv()) {
                    $this->cv = $entity->getCv();
                }
                return;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args): void
    {
        if(isset($this->cv)) {
            $this->cvCalculationDispatcher->dispatch($this->cv);
        }
    }
}
