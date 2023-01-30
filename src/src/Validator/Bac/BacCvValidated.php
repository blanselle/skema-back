<?php

namespace App\Validator\Bac;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class BacCvValidated extends Constraint
{
    public function __construct(
        public string $message = 'Le Cv est déjà validé',
        string|array $options = [],
        array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}