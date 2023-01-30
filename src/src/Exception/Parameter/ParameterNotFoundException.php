<?php

declare(strict_types=1);

namespace App\Exception\Parameter;

use Exception;

class ParameterNotFoundException extends Exception
{
    public function __construct(?string $parameterKey = '', ?string $message = null)
    {
        if (null === $message) {
            $message = sprintf('parameter %s is missing', $parameterKey);
        }
        parent::__construct(
            message: $message,
            code: 500,
        );
    }
}
