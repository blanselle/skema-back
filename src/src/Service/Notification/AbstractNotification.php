<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Entity\Notification\Notification as NotificationEntity;

abstract class AbstractNotification
{
    abstract public function send(NotificationEntity $notificationCenter): void;
}
