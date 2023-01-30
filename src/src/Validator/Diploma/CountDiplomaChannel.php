<?php

declare(strict_types=1);

namespace App\Validator\Diploma;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CountDiplomaChannel extends Constraint
{
    public function __construct(
        public string $message = 'Vous devez spécifier au moins une filière',
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
