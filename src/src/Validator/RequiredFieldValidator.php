<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RequiredFieldValidator extends ConstraintValidator
{
    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof RequiredField)) {
            throw new UnexpectedTypeException($constraint, RequiredField::class);
        }

        $needDetail = (new ExpressionLanguage())->evaluate($constraint->expression, ['this' => $this->context->getObject()]);
        
        if (true === $needDetail && in_array($object, $constraint->nullValues, true)) {
            $this->buildViolation($constraint->message);
        }
    }

    private function buildViolation(string $message): void
    {
        $this->context->buildViolation($message)
            ->setParameter('{{ property }}', $this->context->getPropertyName())
            ->addViolation();
    }
}
