<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use App\Entity\Traits\DateTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class ExamRoom
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'session:collection:read',
    ])]
    private string $name;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'session:collection:read',
    ])]
    private int $numberOfPlaces;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'session:collection:read',
    ])]
    private bool $thirdTime;

    #[ORM\OneToMany(mappedBy: 'examRoom', targetEntity: ExamStudent::class)]
    private Collection $examStudents;

    public function __construct()
    {
        $this->examStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNumberOfPlaces(): ?int
    {
        return $this->numberOfPlaces;
    }

    public function setNumberOfPlaces(int $numberOfPlaces): self
    {
        $this->numberOfPlaces = $numberOfPlaces;

        return $this;
    }

    public function getThirdTime(): ?bool
    {
        return $this->thirdTime;
    }

    public function setThirdTime(bool $thirdTime): self
    {
        $this->thirdTime = $thirdTime;

        return $this;
    }

    /**
     * @return Collection<int, ExamStudent>
     */
    public function getExamStudents(): Collection
    {
        return $this->examStudents;
    }

    public function addExamStudent(ExamStudent $examStudent): self
    {
        if (!$this->examStudents->contains($examStudent)) {
            $this->examStudents[] = $examStudent;
            $examStudent->setExamRoom($this);
        }

        return $this;
    }

    public function removeExamStudent(ExamStudent $examStudent): self
    {
        if ($this->examStudents->removeElement($examStudent)) {
            // set the owning side to null (unless already changed)
            if ($examStudent->getExamRoom() === $this) {
                $examStudent->setExamRoom(null);
            }
        }

        return $this;
    }
}
