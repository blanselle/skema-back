<?php

namespace App\Constants\Bac;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class BacTypeConstants implements ConstantsInterface
{
    public const BAC_TYPES_MODIFICATIONS_YEAR = 2021;

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }

}