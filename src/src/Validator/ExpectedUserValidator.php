<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExpectedUserValidator extends ConstraintValidator
{
    public function __construct(private Security $security)
    {
    }

    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof ExpectedUser)) {
            throw new UnexpectedTypeException($constraint, ExpectedUser::class);
        }

        if ($this->security->getUser()->getUserIdentifier() !== (new ExpressionLanguage())->evaluate($constraint->expression, ['object' => $object])->getUserIdentifier()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
