<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\PlanningInfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlanningInfoRepository::class)]
#[ORM\Table(name: 'oral_test_planning_info')]
class PlanningInfo
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $contestJuryWebsiteCode;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $date;

    #[ORM\OneToMany(mappedBy: 'planningInfo', targetEntity: ExamTest::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $examTests;

    public function __construct()
    {
        $this->examTests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContestJuryWebsiteCode(): string
    {
        return $this->contestJuryWebsiteCode;
    }

    public function setContestJuryWebsiteCode(string $contestJuryWebsiteCode): self
    {
        $this->contestJuryWebsiteCode = $contestJuryWebsiteCode;

        return $this;
    }

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection<int, ExamTest>
     */
    public function getExamTests(): Collection
    {
        return $this->examTests;
    }

    public function addExamTest(ExamTest $examTest): self
    {
        if (!$this->examTests->contains($examTest)) {
            $this->examTests[] = $examTest;
            $examTest->setPlanningInfo($this);
        }

        return $this;
    }

    public function removeExamTest(ExamTest $examTest): self
    {
        if ($this->examTests->removeElement($examTest)) {
            // set the owning side to null (unless already changed)
            if ($examTest->getPlanningInfo() === $this) {
                $examTest->setPlanningInfo(null);
            }
        }

        return $this;
    }
}
