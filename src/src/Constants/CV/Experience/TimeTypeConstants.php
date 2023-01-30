<?php

declare(strict_types=1);

namespace App\Constants\CV\Experience;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class TimeTypeConstants implements ConstantsInterface
{
    public const PARTIAL_TIME = 'partial';
    public const FULL_TIME = 'full';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
