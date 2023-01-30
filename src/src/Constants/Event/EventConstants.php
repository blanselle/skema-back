<?php

declare(strict_types=1);

namespace App\Constants\Event;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class EventConstants implements ConstantsInterface
{
    public const STATUS_PREVIOUS = 'previous';
    public const STATUS_NEXT = 'next';
    public const STATUS_CURRENT = 'current';
    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
