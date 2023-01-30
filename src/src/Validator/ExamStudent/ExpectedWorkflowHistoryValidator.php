<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Exam\ExamStudent;
use App\Entity\User;
use App\Manager\StudentManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExpectedWorkflowHistoryValidator extends ConstraintValidator
{
    public function __construct(private Security $security, private StudentManager $studentManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof ExamStudent) {
            return;
        }

        if (null !== $value->getStudent()) {
            return;
        }

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();
        $currentStudent = $currentUser->getStudent();

        if (
            !$this->studentManager->wentThroughSate(
                $currentStudent,
                [
                    StudentWorkflowStateConstants::STATE_CREATED_TO_PAY,
                    StudentWorkflowStateConstants::STATE_CHECK_BOURSIER
                ]
            )
        ) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
