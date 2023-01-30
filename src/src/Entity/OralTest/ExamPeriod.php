<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\ExamPeriodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExamPeriodRepository::class)]
#[ORM\Table(name: 'oral_test_exam_period')]
class ExamPeriod
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $nbOfJuries;

    #[ORM\OneToMany(mappedBy: 'examPeriod', targetEntity: Jury::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $juries;

    #[ORM\ManyToOne(targetEntity: ExamTest::class, inversedBy: 'examPeriods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExamTest $examTest;

    #[ORM\ManyToOne(inversedBy: 'examPeriods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SlotType $slotType = null;

    public function __construct()
    {
        $this->juries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbOfJuries(): int
    {
        return $this->nbOfJuries;
    }

    public function setNbOfJuries(int $nbOfJuries): self
    {
        $this->nbOfJuries = $nbOfJuries;

        return $this;
    }

    /**
     * @return Collection<int, Jury>
     */
    public function getJuries(): Collection
    {
        return $this->juries;
    }

    public function addJury(Jury $jury): self
    {
        if (!$this->juries->contains($jury)) {
            $this->juries[] = $jury;
            $jury->setExamPeriod($this);
        }

        return $this;
    }

    public function removeJury(Jury $jury): self
    {
        if ($this->juries->removeElement($jury)) {
            // set the owning side to null (unless already changed)
            if ($jury->getExamPeriod() === $this) {
                $jury->setExamPeriod(null);
            }
        }

        return $this;
    }

    public function getExamTest(): ?ExamTest
    {
        return $this->examTest;
    }

    public function setExamTest(?ExamTest $examTest): self
    {
        $this->examTest = $examTest;

        return $this;
    }

    public function getSlotType(): ?SlotType
    {
        return $this->slotType;
    }

    public function setSlotType(?SlotType $slotType): self
    {
        $this->slotType = $slotType;

        return $this;
    }
}
