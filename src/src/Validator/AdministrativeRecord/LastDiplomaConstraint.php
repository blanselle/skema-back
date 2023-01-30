<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class LastDiplomaConstraint extends Constraint
{
    public string $message = 'Error Last diploma : ';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
