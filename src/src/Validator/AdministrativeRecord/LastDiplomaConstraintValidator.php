<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use App\Manager\StudentManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class LastDiplomaConstraintValidator extends ConstraintValidator
{
    public function __construct(private StudentManager $studentManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof LastDiplomaConstraint) {
            throw new UnexpectedTypeException($constraint, LastDiplomaConstraint::class);
        }

        if (null === $this->studentManager->getStudentLastDiploma($this->context->getObject()->getStudent())) {
            $this->context
                ->buildViolation(sprintf(
                    '%s : does not exist',
                    $constraint->message
                ))
                ->addViolation()
            ;
        }
    }
}
