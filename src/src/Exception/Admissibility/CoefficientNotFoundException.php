<?php

declare(strict_types=1);

namespace App\Exception\Admissibility;

use Exception;

class CoefficientNotFoundException extends Exception
{
    public function __construct(string $blocKey = '')
    {
        parent::__construct(
            message: sprintf('Tous les coefficients ne sont pas définis pour la/les voie(s) de concours : %s.', $blocKey),
            code: 500,
        );
    }
}