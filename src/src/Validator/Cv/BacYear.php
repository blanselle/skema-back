<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class BacYear extends Constraint
{
    public function __construct(
        public string $dateOfBirthMessage = 'Vous devez avoir obtenu le baccalauréat après {{ expected }}',
        public string $firstBacSupMessage = 'Vous devez avoir obtenu le baccalauréat avant {{ expected }}',
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
