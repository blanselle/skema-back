<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\TestConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TestConfigurationRepository::class)]
#[ORM\Table(name: 'oral_test_test_configuration')]
class TestConfiguration
{
    use DateTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'La durée du test doit être supérieure à 0.')]
    private ?int $durationOfTest = null;

    #[ORM\Column(nullable: true)]
    private ?int $preparationTime = null;

    #[ORM\ManyToOne(inversedBy: 'testConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestType $testType = null;

    #[ORM\Column(options: ["default" => false])]
    private bool $eveningEvent = false;

    #[ORM\OneToMany(mappedBy: 'testConfiguration', targetEntity: SlotConfiguration::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    #[Assert\Count(min: 2, max: 3, minMessage: 'Vous devez avoir au moins 2 configurations de slot.', maxMessage: 'Vous ne pouvez pas avoir plus de 3 configurations de slot.')]
    private Collection $slotConfigurations;

    #[ORM\ManyToOne(inversedBy: 'testConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CampusConfiguration $campusConfiguration = null;

    public function __construct()
    {
        $this->slotConfigurations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDurationOfTest(): ?int
    {
        return $this->durationOfTest;
    }

    public function setDurationOfTest(int $durationOfTest): self
    {
        $this->durationOfTest = $durationOfTest;

        return $this;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(?int $preparationTime): self
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    public function getTestType(): ?TestType
    {
        return $this->testType;
    }

    public function setTestType(?TestType $testType): self
    {
        $this->testType = $testType;

        return $this;
    }

    public function isEveningEvent(): bool
    {
        return $this->eveningEvent;
    }

    public function setEveningEvent(bool $eveningEvent): self
    {
        $this->eveningEvent = $eveningEvent;

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
            $slotConfiguration->setTestConfiguration($this);
        }

        return $this;
    }

    public function removeSlotConfiguration(SlotConfiguration $slotConfiguration): self
    {
        if ($this->slotConfigurations->removeElement($slotConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($slotConfiguration->getTestConfiguration() === $this) {
                $slotConfiguration->setTestConfiguration(null);
            }
        }

        return $this;
    }

    public function getCampusConfiguration(): ?CampusConfiguration
    {
        return $this->campusConfiguration;
    }

    public function setCampusConfiguration(?CampusConfiguration $campusConfiguration): self
    {
        $this->campusConfiguration = $campusConfiguration;

        return $this;
    }
}
