<?php

declare(strict_types=1);

namespace App\Entity\User;

use App\Entity\Student;
use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class StudentWorkflowHistory
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'user:item:read',
        'user:collection:read',
    ])]
    private int $id;

    #[ORM\Column]
    #[Assert\NotNull]
    private string $state;

    #[ORM\Column]
    #[Assert\NotNull]
    private string $transition;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'workflowHistories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Student $student;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): StudentWorkflowHistory
    {
        $this->id = $id;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): StudentWorkflowHistory
    {
        $this->state = $state;

        return $this;
    }

    public function getTransition(): string
    {
        return $this->transition;
    }

    public function setTransition(string $transition): StudentWorkflowHistory
    {
        $this->transition = $transition;

        return $this;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): StudentWorkflowHistory
    {
        $this->student = $student;
        return $this;
    }
}
