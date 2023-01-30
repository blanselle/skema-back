<?php

declare(strict_types=1);

namespace App\Constants\Notification;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class NotificationTemplateTagConstants implements ConstantsInterface
{
    public const TAG_MEDIA_TRANSFER = 'media_transfer';
    public const TAG_MEDIA_REJECTION = 'media_rejection';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
