<?php

declare(strict_types=1);

namespace App\Constants\Exam;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ExamSessionTypeCodeConstants implements ConstantsInterface
{
    public const ANG = 'ANG';
    public const MANAGEMENT = 'MANAGEMENT';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}