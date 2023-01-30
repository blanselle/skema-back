<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Constants\Notification\NotificationConstants;
use App\Entity\Notification\Notification;

class Notifier
{
    public function __construct(
        private MailNotification $mailNotification,
        private DbNotification $dbNotification,
    ) {
    }

    public function send(Notification $notification, array $code = [], bool $sendGenericMail = true): void
    {
        if (in_array(NotificationConstants::TRANSPORT_DB, $code, true)) {
            $this->dbNotification->send($notification);
        }
        if (in_array(NotificationConstants::TRANSPORT_EMAIL, $code, true)) {
            $this->mailNotification->send($notification, $sendGenericMail);
        }
    }
}
