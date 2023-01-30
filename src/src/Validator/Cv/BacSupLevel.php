<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class BacSupLevel extends Constraint
{
    public function __construct(
        public string $message = 'Veuillez saisir au moins {{ min }} année(s) post baccalauréat (la saisie des bulletins est facultative pour l’année scolaire en cours)',
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
