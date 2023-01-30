<?php

namespace App\Validator\Experience;

use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Entity\CV\Experience;
use DateTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExperienceDateValidator extends ConstraintValidator
{
    public function validate(mixed $date, Constraint $constraint): void
    {
        if (!$constraint instanceof ExperienceDate) {
            throw new UnexpectedTypeException($constraint, ExperienceDate::class);
        }

        /** @var Experience $experience */
        $experience = $this->context->getObject();

        if (null === $experience->getCv()?->getBac()) {
            return;
        }

        $property = match($experience->getExperienceType()) {
            ExperienceTypeConstants::TYPE_ASSOCIATIVE => "associatives",
            ExperienceTypeConstants::TYPE_PROFESSIONAL => "professionnelles",
            ExperienceTypeConstants::TYPE_INTERNATIONAL => "internationales",
            default => ""
        };

        if (
            $experience->getBeginAt()->format('Y') < $experience->getCv()->getBac()->getRewardedYear() or
            $experience->getBeginAt()->format('Y') > (new DateTime('now'))->format('Y')
        ) {
            $this->buildViolation($constraint->message, $property);
        }
    }

    private function buildViolation(string $message, string $property): void
    {
        $this->context->buildViolation($message)
            ->setParameter('{{ property }}', $property)
            ->addViolation();
    }
}