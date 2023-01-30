<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class CandidacyConstants implements ConstantsInterface
{
    public const FORBIDDEN  = 'forbidden';
    public const TO_DO      = 'to_do';
    public const DONE       = 'done';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
