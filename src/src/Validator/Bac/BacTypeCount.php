<?php

declare(strict_types=1);

namespace App\Validator\Bac;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class BacTypeCount extends Constraint
{
    public function __construct(
        public string $message = 'Nombre de spécialités invalide',
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
