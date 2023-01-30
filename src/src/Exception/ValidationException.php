<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends \Exception
{
    private ConstraintViolationListInterface $violations;

    public function __construct(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        parent::__construct('');
    }

    public function getMessages(): array
    {
        $messages = [];
        foreach ($this->violations as $violation) {
            $messages[$violation->getPropertyPath()][] = $violation->getMessage();
        }

        return $messages;
    }
}
