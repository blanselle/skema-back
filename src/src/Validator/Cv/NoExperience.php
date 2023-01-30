<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NoExperience extends Constraint
{
    public function __construct(
        public string $message = 'Veuillez cocher [Je n\'ai aucune expérience %1$s] si vous n\'avez pas d\'expériences %1$s post baccalauréat',
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
