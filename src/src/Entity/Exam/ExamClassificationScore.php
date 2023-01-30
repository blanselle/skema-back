<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class ExamClassificationScore
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ExamClassification::class, inversedBy: 'examClassificationScores')]
    #[ORM\JoinColumn(nullable: false)]
    private ExamClassification $examClassification;

    #[ORM\Column(type: 'float')]
    #[Groups([
        'classification:collection:read',
        'session:collection:read',
    ])]
    private float $score;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ExamClassificationScore
    {
        $this->id = $id;

        return $this;
    }

    public function getExamClassification(): ExamClassification
    {
        return $this->examClassification;
    }

    public function setExamClassification(ExamClassification $examClassification): ExamClassificationScore
    {
        $this->examClassification = $examClassification;

        return $this;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): ExamClassificationScore
    {
        $this->score = $score;

        return $this;
    }
}
