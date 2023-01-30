<?php

declare(strict_types=1);

namespace App\Validator\Experience;

use App\Constants\CV\Experience\TimeTypeConstants;
use App\Entity\CV\Experience;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HoursPerWeekMandatoryForPartialTimeValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$this->context->getObject() instanceof Experience) {
            return;
        }

        if (TimeTypeConstants::PARTIAL_TIME === $this->context->getObject()->getTimeType()&& null === $value) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
