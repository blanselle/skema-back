<?php

declare(strict_types=1);

namespace App\Validator\AdministrativeRecord;

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StudentConstraintValidator extends ConstraintValidator
{
    public function __construct(private Security $security)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof StudentConstraint) {
            throw new UnexpectedTypeException($constraint, StudentConstraint::class);
        }

        if ($this->security->getUser()->getStudent() !== $this->context->getObject()->getStudent())
        {
            $this->context
                ->buildViolation('This administrative record does not belong to this user',
                    $constraint->message
                )
                ->addViolation();
        }
    }
}