<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Entity\Notification\Notification;
use App\Repository\Notification\NotificationRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Security;

class NotificationCenter
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security,
        private NotificationTransformer $notificationTransformer,
        private MessageBusInterface $bus,
    ) {
    }

    public function dispatch(Notification $notification, array $codes = [], bool $sendGenericMail = true): void
    {
        $notificationMessage = $this->notificationTransformer->transform($notification, $codes, $sendGenericMail);
        $this->bus->dispatch($notificationMessage);
    }

    public function countUnreadNotificationsByUser(): int
    {
        $queryBuilder = $this->notificationRepository->createQueryBuilder('a');

        $queryBuilder = $this->notificationRepository->filterQueryBuilder(
            $queryBuilder,
            [
                'filters' => ['read' => false],
                'user' => $this->security->getUser(),
            ]
        );

        return $this->notificationRepository->countResult($queryBuilder);
    }
}
