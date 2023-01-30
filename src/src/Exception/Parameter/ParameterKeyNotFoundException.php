<?php

namespace App\Exception\Parameter;

use Exception;

class ParameterKeyNotFoundException extends Exception
{
    public function __construct(?string $parameterKey = '', ?string $message = null)
    {
        if (null === $message) {
            $message = sprintf('parameter key %s is missing', $parameterKey);
        }
        parent::__construct(
            message: $message,
            code: 500,
        );
    }
}