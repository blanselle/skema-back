<?php

declare(strict_types=1);

namespace App\Constants\Admissibility\Bonus;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class BonusInfoLabelConstants implements ConstantsInterface
{
    public const DISTINCTION_LABEL = 'Mention';
    public const BAC_TYPE_LABEL = 'Type de bac';
    public const DURATION_LABEL = 'Durée';
    public const LEVEL_LABEL = 'Niveau';
    public const EXPERIENCE_TYPE_LABEL = 'Type';
    public const MINIMUM_LABEL = 'Minimum';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
