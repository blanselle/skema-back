<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;

/**
 * Verifie que pour un schoolReport d'un bac+1 la note est bien renseignée
 * 
 * On verifie aussi pour l'ast2 son bac+2
 */
#[\Attribute]
class SchoolReportScoreMissing extends Constraint
{
    public function __construct(
        public string $message = 'La note est obligatoire',
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
