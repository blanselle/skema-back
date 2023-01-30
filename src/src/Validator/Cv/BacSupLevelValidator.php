<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacSupLevelValidator extends ConstraintValidator
{
    public function validate(mixed $bacSups, Constraint $constraint): void
    {
        if (!($constraint instanceof BacSupLevel)) {
            throw new UnexpectedTypeException($constraint, BacSupLevel::class);
        }

        $programChannel = $this->context->getObject()->getStudent()->getProgramChannel();

        $nbBacSupMin = 0;
        if (null !== $programChannel && in_array($programChannel->getName(), ['AST 1', 'AST 2'])) {
            $nbBacSupMin = 2;
        }

        if (count($bacSups) < $nbBacSupMin) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ min }}', strval($nbBacSupMin))
                ->addViolation()
            ;
        }
    }
}
