<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class SchoolReportCount extends Constraint
{
    /**
     * Valid the length of schoolReport in bacSup
     *
     * @param string $message error message
     */
    public function __construct(
        public string $message = 'Le nombre de bulletin n\'est pas valide',
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
