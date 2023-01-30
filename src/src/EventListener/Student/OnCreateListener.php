<?php

declare(strict_types=1);

namespace App\EventListener\Student;

use App\Entity\Student;
use App\Service\Mail\AccountActivationMailDispatcher;
use Doctrine\ORM\Event\PreFlushEventArgs;

class OnCreateListener
{
    public function __construct(private AccountActivationMailDispatcher $mailer) {}

    public function preFlush(PreFlushEventArgs $args): void
    {
        foreach($args->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions() as $entity) {
            if($entity instanceof Student){
                $this->mailer->dispatch($entity);
            }
        }
    }
}
