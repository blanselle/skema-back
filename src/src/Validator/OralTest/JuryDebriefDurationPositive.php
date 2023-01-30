<?php

namespace App\Validator\OralTest;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class JuryDebriefDurationPositive extends Constraint
{
    public function __construct(
        public string $message = 'La durée de debrief (Jury) doit être supérieure à 0.',
        string|array $options = [],
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}