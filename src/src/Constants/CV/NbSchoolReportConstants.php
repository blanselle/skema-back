<?php

declare(strict_types=1);

namespace App\Constants\CV;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class NbSchoolReportConstants implements ConstantsInterface
{
    public const NB_MAX_SCHOOL_REPORT_AST1 = 2;
    public const NB_MAX_SCHOOL_REPORT_AST2 = 3;

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
