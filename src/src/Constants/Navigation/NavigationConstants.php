<?php

declare(strict_types=1);

namespace App\Constants\Navigation;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class NavigationConstants implements ConstantsInterface
{
    public const CONFIG_FILE = __DIR__ . '/../../../config/custom/navigation.yaml';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
