<?php

declare(strict_types=1);

namespace App\Constants;

use ReflectionClass;

class DatatableConstants implements ConstantsInterface
{
    public const TABLE_PAGINATION_START = 0;
    public const TABLE_PAGINATION_LENGTH = 100;

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
