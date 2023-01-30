<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class UserRoleConstants implements ConstantsInterface
{
    public const ROLE_CANDIDATE = 'ROLE_CANDIDATE';
    public const ROLE_COORDINATOR = 'ROLE_COORDINATOR';
    public const ROLE_RESPONSABLE = 'ROLE_RESPONSABLE';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
