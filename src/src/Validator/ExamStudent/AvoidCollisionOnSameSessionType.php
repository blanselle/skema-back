<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[\Attribute]
class AvoidCollisionOnSameSessionType extends Constraint
{
    public string $message = 'Vous ne pouvez pas vous inscrire à deux sessions le même jour sur le même campus et sur le même type de test';

    #[HasNamedArguments]
    public function __construct(array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
