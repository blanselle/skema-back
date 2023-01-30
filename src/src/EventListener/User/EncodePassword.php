<?php

declare(strict_types=1);

namespace App\EventListener\User;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EncodePassword
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->managePassword($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->managePassword($args);
    }

    private function managePassword(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof User) {
            return;
        }

        if (null === $entity->getPlainPassword()) {
            return;
        }

        $hashedPassword = $this->userPasswordHasher->hashPassword(
            $entity,
            $entity->getPlainPassword()
        );

        $entity->setPassword($hashedPassword);
        $entity->setPlainPassword(null);
    }
}
