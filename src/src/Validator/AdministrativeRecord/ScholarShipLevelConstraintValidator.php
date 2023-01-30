<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ScholarShipLevelConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ScholarShipLevelConstraint) {
            throw new UnexpectedTypeException($constraint, ScholarShipLevelConstraint::class);
        }

        if (
            true === $this->context->getObject()->getScholarShip()
            && null === $value
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : Scholarship level is mandatory if scholarship is true',
                    $constraint->message
                ))
                ->addViolation();
        }

        if (
            false === $this->context->getObject()->getScholarShip()
            && null !== $value
        ) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : There must be none scholarship level if high level scholarship is false',
                    $constraint->message
                ))
                ->addViolation();
        }
    }
}
