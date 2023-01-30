<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[\Attribute]
class ExpectedWorkflowHistory extends Constraint
{
    public string $message = 'Vous ne pouvez pas vous inscire à une session car vous n\'avez pas encore validé votre dossier administratif ou payer vos frais de concours.';

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
