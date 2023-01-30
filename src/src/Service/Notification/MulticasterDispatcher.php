<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Model\Notification\MulticastNotification;
use App\Entity\User;
use App\Message\MulticastMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class MulticasterDispatcher
{
    public function __construct(private MessageBusInterface $bus) {}

    public function dispatch(MulticastNotification $notification, array $studentIds, ?User $sender = null): void
    {
        $this->bus->dispatch((new MulticastMessage())
            ->setStudentIds($studentIds)
            ->setSubject($notification->getSubject())
            ->setContent($notification->getContent())
            ->setRoleSender(null != $sender ? $sender->getRoles() : []) 
            ->setSenderId(null != $sender ? (string)$sender->getId() : null)
        );
    }
}
