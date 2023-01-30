<?php

declare(strict_types=1);

namespace App\Validator\Parameter;

use App\Entity\Parameter\Parameter;
use DateTimeInterface;

class LessOrEqualThanParameterValidator extends AbstractComparatorParameterValidator
{
    protected function comparaison(DateTimeInterface|int|float $value, Parameter $parameter): bool
    {
        return $value <= $parameter->getValue();
    }
}
