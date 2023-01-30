<?php

namespace App\Entity\OralTest;

use App\Entity\Exam\ExamLanguage;
use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\ExamTestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamTestRepository::class)]
#[ORM\Table(name: 'oral_test_exam_test')]
class ExamTest
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToMany(mappedBy: 'examTest', targetEntity: ExamPeriod::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $examPeriods;

    #[ORM\ManyToOne(targetEntity: PlanningInfo::class, inversedBy: 'examTests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PlanningInfo $planningInfo;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExamLanguage $examLanguage = null;

    public function __construct()
    {
        $this->examPeriods = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ExamPeriod>
     */
    public function getExamPeriods(): Collection
    {
        return $this->examPeriods;
    }

    public function addExamPeriode(ExamPeriod $examPeriod): self
    {
        if (!$this->examPeriods->contains($examPeriod)) {
            $this->examPeriods[] = $examPeriod;
            $examPeriod->setExamTest($this);
        }

        return $this;
    }

    public function removeExamPeriod(ExamPeriod $examPeriod): self
    {
        if ($this->examPeriods->removeElement($examPeriod)) {
            // set the owning side to null (unless already changed)
            if ($examPeriod->getExamTest() === $this) {
                $examPeriod->setExamTest(null);
            }
        }

        return $this;
    }

    public function getPlanningInfo(): ?PlanningInfo
    {
        return $this->planningInfo;
    }

    public function setPlanningInfo(?PlanningInfo $planningInfo): self
    {
        $this->planningInfo = $planningInfo;

        return $this;
    }

    public function getExamLanguage(): ?ExamLanguage
    {
        return $this->examLanguage;
    }

    public function setExamLanguage(?ExamLanguage $examLanguage): self
    {
        $this->examLanguage = $examLanguage;

        return $this;
    }
}
