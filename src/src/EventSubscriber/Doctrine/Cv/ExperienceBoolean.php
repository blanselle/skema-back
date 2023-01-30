<?php

declare(strict_types=1);

namespace App\EventSubscriber\Doctrine\Cv;

use App\Entity\CV\Experience;
use App\Manager\ExperienceManager;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ExperienceBoolean implements EventSubscriberInterface
{
    public function __construct(private ExperienceManager $experienceManager)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
            Events::preUpdate => 'preUpdate',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->manageBooleanExperience($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->manageBooleanExperience($args);
    }

    private function manageBooleanExperience(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Experience) {
            return;
        }

        $this->experienceManager->unActiveBooleanExperienceInCv($entity);
    }
}
