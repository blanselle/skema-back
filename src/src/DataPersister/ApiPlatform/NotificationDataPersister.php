<?php

declare(strict_types=1);

namespace App\DataPersister\ApiPlatform;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Notification\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class NotificationDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Notification;
    }

    public function persist($notification, array $context = []): void
    {
        if (($context['collection_operation_name'] ?? null) === 'post' ||
            ($context['graphql_operation_name'] ?? null) === 'create'
        ) {
            /** @var User $currentUser */
            $currentUser = $this->security->getUser();
            $notification->setIdentifier(strval($currentUser->getStudent()->getIdentifier()));
            $notification->setSender($currentUser);
            $notification->setRoles(['ROLE_COORDINATOR', 'ROLE_RESPONSABLE']);
        }
        $this->em->persist($notification);
        $this->em->flush();
    }

    public function remove($data, array $context = []): void
    {
    }
}
