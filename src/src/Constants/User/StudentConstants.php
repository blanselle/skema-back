<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class StudentConstants implements ConstantsInterface
{
    public const STUDENT_GENDER_MALE = 'M';
    public const STUDENT_GENDER_FEMALE = 'F';
    public const STUDENT_GENDER_OTHER = 'O';
    public const STUDENT_LIST_GENDER = [
        self::STUDENT_GENDER_MALE,
        self::STUDENT_GENDER_FEMALE,
        self::STUDENT_GENDER_OTHER
    ];
    public const VALUE_VALIDATE = 'validate';
    public const VALUE_REJECTED = 'rejected';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
