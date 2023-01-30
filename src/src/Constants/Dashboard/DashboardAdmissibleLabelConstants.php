<?php

namespace App\Constants\Dashboard;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class DashboardAdmissibleLabelConstants implements ConstantsInterface
{
    public const ADMISSIBLED_REGISTERED = 'Admissibles inscrits aux oraux';
    public const ADMISSIBLED_NOT_REGISTERED = 'Admissibles non inscrits aux oraux';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}