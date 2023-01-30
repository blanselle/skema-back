<?php

declare(strict_types=1);

namespace App\Constants\ProgramChannel;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ProgramChannelKeyConstants implements ConstantsInterface
{
    public const AST1 = 'ast1';
    public const AST2 = 'ast2';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
