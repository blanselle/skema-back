<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use App\Entity\Exam\ExamStudent;
use App\Entity\User;
use App\Exception\ExamStudent\CollisionException;
use App\Manager\StudentManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AvoidCollisionOnOtherCampusValidator extends ConstraintValidator
{
    public function __construct(private Security $security, private StudentManager $studentManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof ExamStudent) {
            return;
        }

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        if (null === $value->getStudent()) {
            $value->setStudent($currentUser->getStudent());
        }

        if (null === $value->getStudent()) {
            throw new CollisionException('Student must be defined');
        }

        if (
            $this->studentManager->hasAlreadyApplyToSessionTheSameDayOnOtherCampus(
                $value->getStudent(),
                $value->getExamSession()
            )
        ) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
