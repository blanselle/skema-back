<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class BacSupYear extends Constraint
{
    public function __construct(
        public string $message = 'L’année de diplomation n’est pas cohérente avec vos informations précédentes',
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
