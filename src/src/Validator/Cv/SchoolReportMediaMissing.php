<?php

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SchoolReportMediaMissing extends Constraint
{
    public function __construct(
        public string $message = 'Le media est obligatoire si vous avez saisie une note',
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