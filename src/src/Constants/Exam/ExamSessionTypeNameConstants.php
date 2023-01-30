<?php

declare(strict_types=1);

namespace App\Constants\Exam;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ExamSessionTypeNameConstants implements ConstantsInterface
{
    public const TYPE_ENGLISH = 'Anglais';
    public const TYPE_MANAGEMENT = 'Management';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}