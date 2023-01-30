<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExpressionOnCollectionValidator extends ConstraintValidator
{
    public function __construct(private Security $security)
    {
    }

    public function validate(mixed $objects, Constraint $constraint): void
    {
        if (!($constraint instanceof ExpressionOnCollection)) {
            throw new UnexpectedTypeException($constraint, ExpressionOnCollection::class);
        }

        foreach ($objects as $object) {
            if (false === (new ExpressionLanguage())->evaluate($constraint->expression, ['item' => $object, 'this' => $this->context->getObject()])) {
                $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ item }}', sprintf('%s', $object->getName()))
                        ->setParameter('{{ property }}', sprintf('%s', $this->context->getPropertyName()))
                        ->addViolation()
                ;
                return;
            }
        }
    }
}
