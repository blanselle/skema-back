<?php

declare(strict_types=1);

namespace App\Validator\Bac;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacOptionValidator extends ConstraintValidator
{
    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof BacOption)) {
            throw new UnexpectedTypeException($constraint, BacOption::class);
        }

        $finded = false;
        $needBacOption = false;

        foreach ($this->context->getObject()->getBacTypes() as $bacType) {
            if (count($bacType->getBacOptions()) > 0) {
                $needBacOption = true;
            }
            if ($bacType->getBacOptions()->contains($this->context->getObject()->getBacOption())) {
                $finded = true;
            }
        }

        if (false === $finded ^ false === $needBacOption) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ property }}', sprintf('%s', $this->context->getPropertyName()))
                ->addViolation()
            ;
        }
    }
}
