<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class HighLevelSportsmanMediasConstraint extends Constraint
{
    public string $message = 'Administrative Record is not valid';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
