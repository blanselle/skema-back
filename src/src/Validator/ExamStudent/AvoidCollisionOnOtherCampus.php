<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[\Attribute]
class AvoidCollisionOnOtherCampus extends Constraint
{
    public string $message = 'Vous ne pouvez pas vous inscrire à deux sessions le même jour sur un campus différent';

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
