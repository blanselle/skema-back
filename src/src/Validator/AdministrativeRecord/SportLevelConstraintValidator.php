<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SportLevelConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof SportLevelConstraint) {
            throw new UnexpectedTypeException($constraint, SportLevelConstraint::class);
        }

        if (
            true === $this->context->getObject()->getHighLevelSportsman()
            && null === $value
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : Sport level is mandatory if high level sportsman is true',
                    $constraint->message
                ))
                ->addViolation();
        }

        if (
            false === $this->context->getObject()->getHighLevelSportsman()
            && null !== $value
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : There must be none sport level if high level sportsman is false',
                    $constraint->message
                ))
                ->addViolation();
        }
    }
}
