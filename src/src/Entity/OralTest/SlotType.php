<?php

namespace App\Entity\OralTest;


use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\SlotTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotTypeRepository::class)]
#[ORM\Table(name: 'oral_test_slot_type')]
class SlotType
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\Column(length: 50, options: ["comment" => "M morning or A afternoon or S evening"])]
    private ?string $code = null;

    #[ORM\Column(options: ["comment" => "1: M, 2: A, 3: S"])]
    private ?int $position = null;

    #[ORM\OneToMany(mappedBy: 'slotType', targetEntity: SlotConfiguration::class)]
    private Collection $slotConfigurations;

    #[ORM\OneToMany(mappedBy: 'slotType', targetEntity: ExamPeriod::class)]
    private Collection $examPeriods;

    public function __construct()
    {
        $this->slotConfigurations = new ArrayCollection();
        $this->examPeriods = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->label;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, SlotConfiguration>
     */
    public function getSlotConfigurations(): Collection
    {
        return $this->slotConfigurations;
    }

    public function addSlotConfiguration(SlotConfiguration $slotConfiguration): self
    {
        if (!$this->slotConfigurations->contains($slotConfiguration)) {
            $this->slotConfigurations->add($slotConfiguration);
            $slotConfiguration->setSlotType($this);
        }

        return $this;
    }

    public function removeSlotConfiguration(SlotConfiguration $slotConfiguration): self
    {
        if ($this->slotConfigurations->removeElement($slotConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($slotConfiguration->getSlotType() === $this) {
                $slotConfiguration->setSlotType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ExamPeriod>
     */
    public function getExamPeriods(): Collection
    {
        return $this->examPeriods;
    }

    public function addExamPeriod(ExamPeriod $examPeriod): self
    {
        if (!$this->examPeriods->contains($examPeriod)) {
            $this->examPeriods->add($examPeriod);
            $examPeriod->setSlotType($this);
        }

        return $this;
    }

    public function removeExamPeriod(ExamPeriod $examPeriod): self
    {
        if ($this->examPeriods->removeElement($examPeriod)) {
            // set the owning side to null (unless already changed)
            if ($examPeriod->getSlotType() === $this) {
                $examPeriod->setSlotType(null);
            }
        }

        return $this;
    }
}
