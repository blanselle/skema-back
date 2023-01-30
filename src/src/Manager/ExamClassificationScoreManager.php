<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamClassificationScore;
use Doctrine\ORM\EntityManagerInterface;

class ExamClassificationScoreManager
{
    public function importNewScores(EntityManagerInterface $em, ExamClassification $examClassification, array $newScores): void
    {
        $currentScores = $em->getRepository(ExamClassificationScore::class)->findBy(['examClassification' => $examClassification]);
        foreach ($currentScores as $currentScore) {
            $em->remove($currentScore);
        }
        foreach ($newScores as $newScore) {
            $score = new ExamClassificationScore();
            $score
                ->setExamClassification($examClassification)
                ->setScore($newScore);
            $em->persist($score);
        }

        $em->flush();
    }

    public function scoreExists(ExamClassification $examClassification, float $score): bool
    {
        foreach ($examClassification->getExamClassificationScores() as $classificationScore) {
            if ($score == $classificationScore->getScore()) {
                return true;
            }
        }

        return false;
    }
}
