<?php

declare(strict_types=1);

namespace App\Validator\Experience;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class HoursPerWeekMandatoryForPartialTime extends Constraint
{
    public string $message = 'hoursPerWeek is mandatory if timetype is partial';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
