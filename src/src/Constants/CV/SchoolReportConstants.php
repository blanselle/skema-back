<?php

declare(strict_types=1);

namespace App\Constants\CV;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class SchoolReportConstants implements ConstantsInterface
{
    public const TYPE_SEMESTRIAL = 'semestriel';
    public const TYPE_ANNUAL = 'annuel';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
