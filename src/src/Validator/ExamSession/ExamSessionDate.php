<?php

declare(strict_types=1);

namespace App\Validator\ExamSession;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[\Attribute]
class ExamSessionDate extends Constraint
{
    public string $message = 'La date doit être comprise entre {dateStart} et {dateEnd}';

    #[HasNamedArguments]
    public function __construct(array $groups = null, mixed $payload = null)
    {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
