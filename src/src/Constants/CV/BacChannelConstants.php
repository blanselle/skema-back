<?php

declare(strict_types=1);

namespace App\Constants\CV;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class BacChannelConstants implements ConstantsInterface
{
    public const GENERAL = 'general';
    public const PROFESSIONAL = 'professional';
    public const TECHNOLOGIE = 'technologique';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
