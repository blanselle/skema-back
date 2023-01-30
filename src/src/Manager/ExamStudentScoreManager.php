<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamClassificationScore;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ExamStudentScoreManager
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function hasNoOneCandidateWithGivenIdentity(ExamSession $examSession, string $lastName, string $firstName, Datetime $birth): bool
    {
        return (count($this->entityManager->getRepository(ExamStudent::class)->findExamStudentWithIdentityBySession(
            $examSession,
            $lastName,
            $firstName,
            $birth
        )) == 0);
    }

    public function hasMultipleCandidatesWithSameIdentity(ExamSession $examSession, string $lastName, string $firstName, Datetime $birth): bool
    {
        return (count($this->entityManager->getRepository(ExamStudent::class)->findExamStudentWithIdentityBySession(
            $examSession,
            $lastName,
            $firstName,
            $birth
        )) > 1);
    }

    public function isNotPresentInClassificationScoresPossibilites(ExamClassification $examClassification, float $score): bool
    {
        return empty($this->entityManager->getRepository(ExamClassificationScore::class)->findBy(
            ['examClassification' => $examClassification, 'score' =>$score]
        ));
    }
}
