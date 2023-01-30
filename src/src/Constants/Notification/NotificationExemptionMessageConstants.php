<?php

declare(strict_types=1);

namespace App\Constants\Notification;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class NotificationExemptionMessageConstants implements ConstantsInterface
{
    public const NOTIFICATION_CHECK_DIPLOMA = 'NOTIFICATION_CHECK_DIPLOMA';
    public const NOTIFICATION_DEROGATION_VALIDATED = 'NOTIFICATION_DEROGATION_VALIDATED';
    public const NOTIFICATION_PAYMENT_VALIDATED = 'NOTIFICATION_PAYMENT_VALIDATED';
    public const NOTIFICATION_PAYMENT_REJECTED = 'NOTIFICATION_PAYMENT_REJECTED';
    public const NOTIFICATION_PAYMENT_CANCELED = 'NOTIFICATION_PAYMENT_CANCELED';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
