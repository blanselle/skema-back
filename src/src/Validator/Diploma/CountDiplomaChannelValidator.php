<?php

declare(strict_types=1);

namespace App\Validator\Diploma;

use App\Entity\Diploma\Diploma;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CountDiplomaChannelValidator extends ConstraintValidator
{
    public function validate(mixed $object, Constraint $constraint): void
    {
        if (!($constraint instanceof CountDiplomaChannel)) {
            throw new UnexpectedTypeException($constraint, CountDiplomaChannel::class);
        }
        
        /** @var Diploma $diploma */
        $diploma = $this->context->getObject();
        
        if($diploma->getNeedDetail() === true) {
            return;
        }
        
        if(count($diploma->getDiplomaChannels()) < 1) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
