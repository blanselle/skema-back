<?php

declare(strict_types=1);

namespace App\Validator\Media;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class MediaFormFileNotNull extends Constraint
{
    public function __construct(
        public string $message = 'Vous devez ajouter un document',
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
