<?php

declare(strict_types=1);

namespace App\Constants\CV\Experience;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ExperienceStateConstants implements ConstantsInterface
{
    public const STATE_ACCEPTED = 'accepted';
    public const STATE_REJECTED = 'rejected';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
