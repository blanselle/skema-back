<?php

declare(strict_types=1);

namespace App\Constants\CV\Experience;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ExperienceTypeConstants implements ConstantsInterface
{
    public const TYPE_PROFESSIONAL = 'professional';
    public const TYPE_INTERNATIONAL = 'international';
    public const TYPE_ASSOCIATIVE = 'associative';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
