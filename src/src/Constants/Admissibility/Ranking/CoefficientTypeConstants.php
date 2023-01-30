<?php

declare(strict_types=1);

namespace App\Constants\Admissibility\Ranking;

use App\Constants\ConstantsInterface;
use App\Constants\Exam\ExamSessionTypeNameConstants;
use ReflectionClass;

class CoefficientTypeConstants implements ConstantsInterface
{
    public const TYPE_CV = 'Cv';
    public const TYPE_ENG = ExamSessionTypeNameConstants::TYPE_ENGLISH;
    public const TYPE_MNGT = ExamSessionTypeNameConstants::TYPE_MANAGEMENT;

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}