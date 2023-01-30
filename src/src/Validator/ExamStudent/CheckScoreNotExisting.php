<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CheckScoreNotExisting extends Constraint
{
    public string $message = 'Le résultat donné {{ score }} n\'existe pas dans la table des scores possibles pour la typologie : {{ classification }}';

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
