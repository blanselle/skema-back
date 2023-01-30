<?php

declare(strict_types=1);

namespace App\Constants\Admissibility\Bonus;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class BonusNameConstants implements ConstantsInterface
{
    public const BAC_TYPE = 'bac_type';
    public const ADDITIONNAL = 'additionnal';
    public const BAC_DISTINCTION = 'bac_distinction';
    public const SPORT_LEVEL = 'sport_level';
    public const LANGUAGE = 'language';
    public const EXPERIENCE = 'experience';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
