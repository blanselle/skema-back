<?php

declare(strict_types=1);

namespace App\Validator\Parameter;


#[\Attribute]
class LessOrEqualThanParameter extends AbstractComparatorParameter
{
    /**
     * Return error if the value is higher than parameter
     *
     * @param string $parameterName Name of the parameter (Parameter.key.name)
     */
    public function __construct(
        public string $parameterName,
        public string $programChannelId,
        public string $message = 'The property {{ property }} is too high',
        string|array $options = [],
        array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct($parameterName, $programChannelId, $options, $groups, $payload);
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
