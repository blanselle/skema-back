<?php

namespace App\Validator\OralTest;

use App\Entity\OralTest\CampusConfiguration;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PreparationRoomNotBlankValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof PreparationRoomNotBlank) {
            throw new UnexpectedTypeException($constraint, PreparationRoomNotBlank::class);
        }

        /** @var CampusConfiguration $object */
        $object = $this->context->getObject();

        if (null === $value and $this->hasPreparationTime(configuration: $object)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function hasPreparationTime(CampusConfiguration $configuration): bool
    {
        foreach($configuration->getTestConfigurations() as $test) {
            if ((int)$test->getPreparationTime() > 0) {
                return true;
            }
        }

        return false;
    }
}