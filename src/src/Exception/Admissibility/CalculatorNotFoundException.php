<?php

namespace App\Exception\Admissibility;

use Exception;

class CalculatorNotFoundException extends Exception
{
    public function __construct(int $calculatorId)
    {
        parent::__construct(
            message: "Calculator with identifier {$calculatorId} for ranking not found",
            code: 500,
        );
    }
}