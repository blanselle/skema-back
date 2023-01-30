<?php

namespace App\Entity\OralTest;

use App\Entity\ProgramChannel;
use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\SudokuConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SudokuConfigurationRepository::class)]
#[ORM\Table(name: 'oral_test_sudoku_configuration')]
class SudokuConfiguration
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'sudokuConfiguration', targetEntity: ProgramChannel::class)]
    #[Assert\Length(min: 1, minMessage: 'Vous devez sélectionner au moins une voie de concours.')]
    private Collection $programChannels;

    #[ORM\OneToMany(mappedBy: 'sudokuConfiguration', targetEntity: CampusConfiguration::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    private Collection $campusConfigurations;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
        $this->campusConfigurations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return 'Sudoku ' . implode(' - ', array_map(fn(ProgramChannel $programChannel) => $programChannel->getName(), $this->programChannels->toArray()));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ProgramChannel>
     */
    public function getProgramChannels(): Collection
    {
        return $this->programChannels;
    }

    public function addProgramChannel(ProgramChannel $programChannel): self
    {
        if (!$this->programChannels->contains($programChannel)) {
            $this->programChannels->add($programChannel);
            $programChannel->setSudokuConfiguration($this);
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        if ($this->programChannels->removeElement($programChannel)) {
            // set the owning side to null (unless already changed)
            if ($programChannel->getSudokuConfiguration() === $this) {
                $programChannel->setSudokuConfiguration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CampusConfiguration>
     */
    public function getCampusConfigurations(): Collection
    {
        return $this->campusConfigurations;
    }

    public function addCampusConfiguration(CampusConfiguration $campusConfiguration): self
    {
        if (!$this->campusConfigurations->contains($campusConfiguration)) {
            $this->campusConfigurations->add($campusConfiguration);
            $campusConfiguration->setSudokuConfiguration($this);
        }

        return $this;
    }

    public function removeCampusConfiguration(CampusConfiguration $campusConfiguration): self
    {
        if ($this->campusConfigurations->removeElement($campusConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($campusConfiguration->getSudokuConfiguration() === $this) {
                $campusConfiguration->setSudokuConfiguration(null);
            }
        }

        return $this;
    }
}
