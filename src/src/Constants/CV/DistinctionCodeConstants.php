<?php

namespace App\Constants\CV;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class DistinctionCodeConstants implements ConstantsInterface
{
    public const DISTINCTION_TRES_BIEN = 'distinction_tb';
    public const DISTINCTION_BIEN = 'distinction_b';
    public const DISTINCTION_ASSEZ_BIEN = 'distinction_ab';
    public const NO_DISTINCTION = 'no_distinction';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
