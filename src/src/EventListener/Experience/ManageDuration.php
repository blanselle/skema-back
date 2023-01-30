<?php

declare(strict_types=1);

namespace App\EventListener\Experience;

use App\Entity\CV\Experience;
use App\Manager\ExperienceManager;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ManageDuration
{
    public function __construct(private ExperienceManager $experienceManager)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->manageDuration($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->manageDuration($args);
    }

    private function manageDuration(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Experience) {
            return;
        }

        $entity->setDuration($this->experienceManager->getDurationForExperience($entity));
    }
}
