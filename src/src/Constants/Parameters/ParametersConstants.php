<?php

declare(strict_types=1);

namespace App\Constants\Parameters;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class ParametersConstants implements ConstantsInterface
{
    public const DEFAULT_COUNTRY = 'FR';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
