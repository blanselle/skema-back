<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\LandingPage;

use App\Entity\Loggable\History;
use App\Entity\Student;
use App\Entity\Traits\DateTrait;
use App\Repository\Admissibility\LandingPage\AdmissibilityStudentTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[Gedmo\Loggable(logEntryClass: History::class)]
#[ORM\Entity(repositoryClass: AdmissibilityStudentTokenRepository::class)]
class AdmissibilityStudentToken
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Student $student;

    #[ORM\Column(type: 'string')]
    #[Gedmo\Versioned]
    private string $token;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}