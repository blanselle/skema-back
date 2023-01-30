<?php

namespace App\Validator\Experience;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ExperienceDate extends Constraint
{
    public function __construct(
        public string $message = 'Merci de ne renseigner que les expériences {{ property }} post baccalauréat.',
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