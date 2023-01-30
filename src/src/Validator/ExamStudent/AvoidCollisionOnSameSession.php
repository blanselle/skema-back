<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[\Attribute]
class AvoidCollisionOnSameSession extends Constraint
{
    public string $message = 'Vous ne pouvez pas vous inscrire deux fois à la même session';

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
