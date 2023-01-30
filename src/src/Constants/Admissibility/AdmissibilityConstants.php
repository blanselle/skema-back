<?php

declare(strict_types=1);

namespace App\Constants\Admissibility;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class AdmissibilityConstants implements ConstantsInterface
{
    public const CALCUL_WITH_BORDERS = 'CALCUL_WITH_BORDERS';
    public const CALCUL_WITH_MEDIAN = 'CALCUL_WITH_MEDIAN';
    public const CALCUL_WITH_IMPORT = 'CALCUL_WITH_IMPORT';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
