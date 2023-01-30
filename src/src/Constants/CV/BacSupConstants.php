<?php

declare(strict_types=1);

namespace App\Constants\CV;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class BacSupConstants implements ConstantsInterface
{
    public const TYPE_SEMESTRIAL = 'semestriel';
    public const TYPE_ANNUAL = 'annuel';

    public const BAC_PLUS_1 = 'Bac+1 (L1)';
    public const BAC_PLUS_2 = 'Bac+2 (L2)';
    public const BAC_PLUS_3 = 'Bac+3 (L3)';
    public const BAC_PLUS_4 = 'Bac+4 (M1)';
    public const BAC_PLUS_5 = 'Bac+5 (M2)';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
