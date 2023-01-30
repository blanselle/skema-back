<?php

declare(strict_types=1);

namespace App\Constants\Admissibility\Bonus;

use App\Constants\ConstantsInterface;
use App\Entity\Admissibility\Bonus\BacDistinctionBonus;
use App\Entity\Admissibility\Bonus\BacTypeBonus;
use App\Entity\Admissibility\Bonus\BasicBonus;
use App\Entity\Admissibility\Bonus\ExperienceBonus;
use App\Entity\Admissibility\Bonus\LanguageBonus;
use App\Entity\Admissibility\Bonus\SportLevelBonus;
use ReflectionClass;

class BonusListConstants implements ConstantsInterface
{
    public const BAC_DISTINCTION = BacDistinctionBonus::class;
    public const BAC_TYPE = BacTypeBonus::class;
    public const ADDITIONNAL = BasicBonus::class;
    public const EXPERIENCE = ExperienceBonus::class;
    public const LANGUAGE = LanguageBonus::class;
    public const SPORT_LEVEL = SportLevelBonus::class;

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
