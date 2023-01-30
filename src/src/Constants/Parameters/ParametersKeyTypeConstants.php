<?php

declare(strict_types=1);

namespace App\Constants\Parameters;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class ParametersKeyTypeConstants implements ConstantsInterface
{
    public const DATE = 'date';
    public const TEXT = 'text';
    public const NUMBER = 'number';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
