<?php

declare(strict_types=1);

namespace App\Validator\ExamStudent;

use App\Entity\Exam\ExamStudent;
use App\Manager\ExamStudentScoreManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckScoreNotExistingValidator extends ConstraintValidator
{
    public function __construct(private ExamStudentScoreManager $scoreManager)
    {
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$value instanceof ExamStudent) {
            return;
        }

        if (null === $value || null === $value->getScore()) {
            return;
        }

        if ($this->scoreManager->isNotPresentInClassificationScoresPossibilites($value->getExamSession()->getExamClassification(), $value->getScore())) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ score }}', sprintf('%d', $value->getScore()))
                ->setParameter('{{ classification }}', sprintf('%s', $value->getExamSession()->getExamClassification()->getName()))
                ->addViolation()
            ;
        }
    }
}
