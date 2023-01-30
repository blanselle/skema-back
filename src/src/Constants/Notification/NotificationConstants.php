<?php

declare(strict_types=1);

namespace App\Constants\Notification;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class NotificationConstants implements ConstantsInterface
{
    public const TRANSPORT_EMAIL = 'email';
    public const TRANSPORT_DB = 'db';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
