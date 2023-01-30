<?php

declare(strict_types=1);

namespace App\Constants\Exam;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ExamSessionTypeConstants implements ConstantsInterface
{
    public const TYPE_INSIDE = 'Skema';
    public const TYPE_OUTSIDE = 'Extérieur';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
