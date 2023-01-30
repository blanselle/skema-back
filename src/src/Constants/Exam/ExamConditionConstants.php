<?php

declare(strict_types=1);

namespace App\Constants\Exam;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class ExamConditionConstants implements ConstantsInterface
{
    public const CONDITION_ONLINE = 'En ligne';
    public const CONDITION_IN_PERSON = 'Présentiel';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
