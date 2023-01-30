<?php

declare(strict_types=1);

namespace App\Validator\Cv;

use App\Constants\CV\Experience\ExperienceTypeConstants;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NoExperienceValidator extends ConstraintValidator
{
    public function validate(mixed $experiences, Constraint $constraint): void
    {
        if (!($constraint instanceof NoExperience)) {
            throw new UnexpectedTypeException($constraint, NoExperience::class);
        }

        $nbExperienceAssociative = 0;
        $nbExperienceProfessional= 0;
        $nbExperienceInternational = 0;

        foreach ($experiences as $experience) {
            if (ExperienceTypeConstants::TYPE_ASSOCIATIVE === $experience->getExperienceType()) {
                $nbExperienceAssociative++;
            }
            if (ExperienceTypeConstants::TYPE_PROFESSIONAL === $experience->getExperienceType()) {
                $nbExperienceProfessional++;
            }
            if (ExperienceTypeConstants::TYPE_INTERNATIONAL === $experience->getExperienceType()) {
                $nbExperienceInternational++;
            }
        }

        if ($nbExperienceAssociative > 0 xor !$this->context->getObject()->getNoAssociativeExperience()) {
            $this->context->buildViolation(sprintf($constraint->message, 'associative'))
                ->setParameter('{{ property }}', 'noExperienceAssociative')
                ->addViolation()
            ;
        }

        if ($nbExperienceProfessional > 0 xor !$this->context->getObject()->getNoProfessionalExperience()) {
            $this->context->buildViolation(sprintf($constraint->message, 'professionnelle'))
                ->setParameter('{{ property }}', 'noExperienceProfessional')
                ->addViolation()
            ;
        }

        if ($nbExperienceInternational > 0 xor !$this->context->getObject()->getNoInternationnalExperience()) {
            $this->context->buildViolation(sprintf($constraint->message, 'internationale'))
                ->setParameter('{{ property }}', 'noExperienceInternational')
                ->addViolation()
            ;
        }
    }
}
