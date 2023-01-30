<?php

namespace App\Validator\Bac;

use App\Entity\CV\Bac\Bac;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacCvValidatedValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $em) {}

    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof BacCvValidated)) {
            throw new UnexpectedTypeException($constraint, BacCvValidated::class);
        }

        /** @var Bac $bac */
        $bac = $this->context->getObject();

        if ($bac->getCv()->getValidated() === false) {
            return;
        }

        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();
        $changes = $uow->getEntityChangeSet($bac);
        $changesKeys = array_keys($changes);

        foreach ($changesKeys as $item) {
            if ($item !== 'media') {
                $this->context->buildViolation($constraint->message)
                    ->addViolation()
                ;
            }
        }
    }
}