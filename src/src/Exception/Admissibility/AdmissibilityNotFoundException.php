<?php

declare(strict_types=1);

namespace App\Exception\Admissibility;

use Exception;

class AdmissibilityNotFoundException extends Exception
{
    public function __construct(?string $message = null)
    {
        if (null === $message) {
            $message = 'An error occurred while generating eligibility scores.';
        }
        parent::__construct(
            message: $message,
            code: 500,
        );
    }
}