<?php

namespace App\Validator\OralTestStudent;

use Symfony\Component\Validator\Attribute\HasNamedArguments;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueAccordingToState extends Constraint
{
    public string $message = 'Vous vous êtes déjà inscrits sur ce créneau d\'épreuve orale. Votre demande est soit en attente, soit déjà validée.';

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