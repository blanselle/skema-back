<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ExpressionOnCollection extends Constraint
{
    public function __construct(
        public string $expression,
        public string $message = 'The item {{ item }} in the property {{ property }} is not valid',
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
