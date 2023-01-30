<?php

declare(strict_types=1);

namespace App\DataPersister\ApiPlatform;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\User;
use App\Service\User\UserEmailAlreadyExists;
use Doctrine\ORM\EntityManagerInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserEmailAlreadyExists $emailAlreadyExists,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function persist($user, array $context = []): void
    {
        if (
            $user instanceof User && (
                ($context['collection_operation_name'] ?? null) === 'post' ||
                ($context['graphql_operation_name'] ?? null) === 'create'
            )
        ) {
            $this->emailAlreadyExists->check($user);
        }

        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove($data, array $context = []): void
    {
    }
}
