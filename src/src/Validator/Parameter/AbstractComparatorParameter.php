<?php

declare(strict_types=1);

namespace App\Validator\Parameter;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
abstract class AbstractComparatorParameter extends Constraint
{
    /**
     * Return error if the value is higher than parameter
     *
     * @param string $parameterName Name of the parameter (Parameter.key.name)
     */
    public function __construct(
        public string $parameterName,
        public string $programChannelId,
        string|array $options = [],
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
