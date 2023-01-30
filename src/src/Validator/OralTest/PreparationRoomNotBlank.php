<?php

namespace App\Validator\OralTest;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class PreparationRoomNotBlank extends Constraint
{
    public function __construct(
        public string $message = 'La salle de préparation est obligatoire.',
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