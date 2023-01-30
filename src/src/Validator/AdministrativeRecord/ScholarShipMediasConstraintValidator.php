<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ScholarShipMediasConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ScholarShipMediasConstraint) {
            throw new UnexpectedTypeException($constraint, ScholarShipMediasConstraint::class);
        }

        if (
            true === $this->context->getObject()->getScholarShip()
            && count($value) <= 0
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : At least one scholarship media is mandatory if scholarship is true',
                    $constraint->message
                ))
                ->addViolation();
        }

        if (
            false === $this->context->getObject()->getScholarShip()
            && count($value) > 0
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : There must be none scholarship media if scholarship is false',
                    $constraint->message
                ))
                ->addViolation();
        }
    }
}
