<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\JuryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JuryRepository::class)]
#[ORM\Table(name: 'oral_test_jury')]
class Jury
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    private string $code;

    #[ORM\Column(type: 'string', length: 10)]
    private string $classRoomNumber;

    #[ORM\Column(type: 'array')]
    private array $examiners = [];

    #[ORM\ManyToOne(targetEntity: ExamPeriod::class, inversedBy: 'juries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExamPeriod $examPeriod;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getClassRoomNumber(): string
    {
        return $this->classRoomNumber;
    }

    public function setClassRoomNumber(string $classRoomNumber): self
    {
        $this->classRoomNumber = $classRoomNumber;

        return $this;
    }

    public function getExaminers(): array
    {
        return $this->examiners;
    }

    public function setExaminers(array $examiners): self
    {
        $this->examiners = $examiners;

        return $this;
    }

    public function getExamPeriod(): ?ExamPeriod
    {
        return $this->examPeriod;
    }

    public function setExamPeriod(?ExamPeriod $examPeriod): self
    {
        $this->examPeriod = $examPeriod;

        return $this;
    }
}
