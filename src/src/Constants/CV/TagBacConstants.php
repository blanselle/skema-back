<?php

declare(strict_types=1);

namespace App\Constants\CV;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class TagBacConstants implements ConstantsInterface
{
    public const V1 = '1';
    public const V2 = '2';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
