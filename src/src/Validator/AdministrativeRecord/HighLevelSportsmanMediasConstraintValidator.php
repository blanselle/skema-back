<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class HighLevelSportsmanMediasConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof HighLevelSportsmanMediasConstraint) {
            throw new UnexpectedTypeException($constraint, HighLevelSportsmanMediasConstraint::class);
        }

        if (
            true === $this->context->getObject()->getHighLevelSportsman()
            && count($value) <= 0
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : At least one high level sportsman media media is mandatory if high level sportsman is true',
                    $constraint->message
                ))
                ->addViolation();
        }

        if (
            false === $this->context->getObject()->getHighLevelSportsman()
            && count($value) > 0
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : There must be none high level sportsman media if high level sportsman is false',
                    $constraint->message
                ))
                ->addViolation();
        }
    }
}
