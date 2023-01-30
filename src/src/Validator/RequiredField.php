<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class RequiredField extends Constraint
{
    /**
     * Valid a field required of the expression is true
     *
     * @param string $expression Condition that makes the field needed
     * @param array $nullValues List of nullable values
     */
    public function __construct(
        public string $expression,
        public string $message = 'The property {{ property }} is required',
        public array $nullValues = [null],
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
