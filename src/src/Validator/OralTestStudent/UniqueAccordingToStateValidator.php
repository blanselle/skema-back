<?php

namespace App\Validator\OralTestStudent;

use App\Constants\OralTest\OralTestStudentWorkflowStateConstants;
use App\Entity\OralTest\OralTestStudent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueAccordingToStateValidator extends ConstraintValidator
{
    public function __construct(private EntityManagerInterface $em) {}

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof OralTestStudent) {
            return;
        }

        $oralTestStudents = $this->em->getRepository(OralTestStudent::class)->findByStudentByState(
            $value->getStudent(),
            [
                OralTestStudentWorkflowStateConstants::WAITING_FOR_TREATMENT,
                OralTestStudentWorkflowStateConstants::VALIDATED,
            ]
        );

        if (empty($oralTestStudents)) {
            return;
        }

        $this->context->buildViolation($constraint->message)->addViolation();
    }
}