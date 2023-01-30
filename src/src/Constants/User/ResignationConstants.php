<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ResignationConstants implements ConstantsInterface
{
    public const RESIGNATION_LABEL_MESSAGE = 'RESIGNATION_LABEL_MESSAGE';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
