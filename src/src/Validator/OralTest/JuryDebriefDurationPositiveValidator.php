<?php

namespace App\Validator\OralTest;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class JuryDebriefDurationPositiveValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof JuryDebriefDurationPositive) {
            throw new UnexpectedTypeException($constraint, JuryDebriefDurationPositive::class);
        }

        if (null !== $value and (int)$value < 0) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}