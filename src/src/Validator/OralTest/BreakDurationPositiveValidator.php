<?php

namespace App\Validator\OralTest;

use App\Constants\OralTest\SlotTypeConstants;
use App\Entity\OralTest\SlotConfiguration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BreakDurationPositiveValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof BreakDurationPositive) {
            throw new UnexpectedTypeException($constraint, BreakDurationPositive::class);
        }

        /** @var SlotConfiguration $object */
        $object = $this->context->getObject();
        $testConfiguration = $object->getTestConfiguration();
        if (
            $object->getSlotType()->getCode() === SlotTypeConstants::TYPE_CODE_EVENING and
            !$testConfiguration->isEveningEvent()
        ) {
            return;
        }

        if (null !== $object->getBreakTime() and (null === $value or (int)$value < 0)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}