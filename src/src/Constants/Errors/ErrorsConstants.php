<?php

declare(strict_types=1);

namespace App\Constants\Errors;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class ErrorsConstants implements ConstantsInterface
{
    public const ERROR_CANDIDATE_INSCRIPTION_NOT_OPEN = 'ERROR_CANDIDATE_INSCRIPTION_NOT_OPEN';
    public const ERROR_CANDIDATE_INSCRIPTION_TOO_LATE = 'ERROR_CANDIDATE_INSCRIPTION_TOO_LATE';
    public const ERROR_CANDIDATE_EXEMPTION = 'ERROR_CANDIDATE_EXEMPTION';
    public const ERROR_CANDIDATE_CONNEXION_FORM = 'ERROR_CANDIDATE_CONNEXION_FORM';
    public const AUTH_REFUSED = 'AUTH_REFUSED';
    public const AUTH_REFUSED_PAYEMENT = 'AUTH_REFUSED_PAYEMENT';
    public const ERROR_CONNEXION_RESIGNATION = 'ERROR_CONNEXION_RESIGNATION';
    public const USER_CREATION_OK = 'USER_CREATION_OK';
    public const UNACTIVE_ACCOUNT = 'ERROR_UNACTIVE_ACCOUNT';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
