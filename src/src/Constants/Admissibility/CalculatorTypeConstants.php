<?php

namespace App\Constants\Admissibility;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class CalculatorTypeConstants implements ConstantsInterface
{
    public const TYPE_RANKING_SIMULATOR = 'ranking_simulator';
    public const TYPE_RANKING_ADMISSIBILITY = 'ranking_admissibility';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}