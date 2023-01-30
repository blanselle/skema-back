<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\SlotConfigurationRepository;
use App\Validator\OralTest\BreakDurationPositive;
use App\Validator\OralTest\BreakTimeNotNull;
use App\Validator\OralTest\DayTimeFieldNotNull;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotConfigurationRepository::class)]
#[ORM\Table(name: 'oral_test_slot_configuration')]
class SlotConfiguration
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    #[DayTimeFieldNotNull(message: 'L\'heure de début est obligatoire.')]
    private ?DateTimeImmutable $startTime = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    #[DayTimeFieldNotNull(message: 'L\'heure de fin est obligatoire.')]
    private ?DateTimeImmutable $endTime = null;

    #[ORM\Column(type: Types::TIME_IMMUTABLE, nullable: true)]
    #[BreakTimeNotNull]
    private ?DateTimeImmutable $breakTime = null;

    #[ORM\Column(nullable: true)]
    #[BreakDurationPositive]
    private ?int $breakDuration = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbOfCandidatesPerJury = null;

    #[ORM\ManyToOne(inversedBy: 'slotConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SlotType $slotType = null;

    #[ORM\ManyToOne(inversedBy: 'slotConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TestConfiguration $testConfiguration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(?DateTimeImmutable $startTime = null): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(?DateTimeImmutable $endTime = null): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getBreakTime(): ?DateTimeImmutable
    {
        return $this->breakTime;
    }

    public function setBreakTime(?DateTimeImmutable $breakTime = null): self
    {
        $this->breakTime = $breakTime;

        return $this;
    }

    public function getBreakDuration(): ?int
    {
        return $this->breakDuration;
    }

    public function setBreakDuration(?int $breakDuration): self
    {
        $this->breakDuration = $breakDuration;

        return $this;
    }

    public function getNbOfCandidatesPerJury(): ?int
    {
        return $this->nbOfCandidatesPerJury;
    }

    public function setNbOfCandidatesPerJury(?int $nbOfCandidatesPerJury): self
    {
        $this->nbOfCandidatesPerJury = $nbOfCandidatesPerJury;

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

    public function getTestConfiguration(): ?TestConfiguration
    {
        return $this->testConfiguration;
    }

    public function setTestConfiguration(?TestConfiguration $testConfiguration): self
    {
        $this->testConfiguration = $testConfiguration;

        return $this;
    }
}
