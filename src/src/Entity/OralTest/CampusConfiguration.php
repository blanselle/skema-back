<?php

namespace App\Entity\OralTest;

use App\Entity\Campus;
use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\CampusConfigurationRepository;
use App\Validator\OralTest\JuryDebriefDurationPositive;
use App\Validator\OralTest\PreparationRoomNotBlank;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CampusConfigurationRepository::class)]
#[ORM\Table(name: 'oral_test_campus_configuration')]
class CampusConfiguration
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Positive(message: 'La durée entre les épreuves doit être supérieure à 0.')]
    #[Assert\NotNull(message: 'La durée entre les épreuves est obligatoire.')]
    private ?int $minimumDurationBetweenTwoTests = null;

    #[ORM\Column(nullable: true)]
    #[JuryDebriefDurationPositive]
    private ?int $juryDebriefDuration = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[PreparationRoomNotBlank]
    private ?string $preparationRoom = null;

    #[ORM\ManyToOne(inversedBy: 'campusConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DistributionType $distribution = null;

    #[ORM\ManyToOne(inversedBy: 'campusConfigurations')]
    private ?Campus $campus = null;

    #[ORM\OneToMany(mappedBy: 'campusConfiguration', targetEntity: TestConfiguration::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid]
    #[Assert\Count(exactly: 2)]
    private Collection $testConfigurations;

    #[ORM\ManyToOne(inversedBy: 'campusConfigurations')]
    private ?SudokuConfiguration $sudokuConfiguration = null;

    public function __construct()
    {
        $this->testConfigurations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMinimumDurationBetweenTwoTests(): ?int
    {
        return $this->minimumDurationBetweenTwoTests;
    }

    public function setMinimumDurationBetweenTwoTests(int $minimumDurationBetweenTwoTests): self
    {
        $this->minimumDurationBetweenTwoTests = $minimumDurationBetweenTwoTests;

        return $this;
    }

    public function getJuryDebriefDuration(): ?int
    {
        return $this->juryDebriefDuration;
    }

    public function setJuryDebriefDuration(?int $juryDebriefDuration): self
    {
        $this->juryDebriefDuration = $juryDebriefDuration;

        return $this;
    }

    public function getPreparationRoom(): ?string
    {
        return $this->preparationRoom;
    }

    public function setPreparationRoom(?string $preparationRoom): self
    {
        $this->preparationRoom = $preparationRoom;

        return $this;
    }

    public function getDistribution(): ?DistributionType
    {
        return $this->distribution;
    }

    public function setDistribution(?DistributionType $distribution): self
    {
        $this->distribution = $distribution;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Collection<int, TestConfiguration>
     */
    public function getTestConfigurations(): Collection
    {
        return $this->testConfigurations;
    }

    public function addTestConfiguration(TestConfiguration $testConfiguration): self
    {
        if (!$this->testConfigurations->contains($testConfiguration)) {
            $this->testConfigurations->add($testConfiguration);
            $testConfiguration->setCampusConfiguration($this);
        }

        return $this;
    }

    public function removeTestConfiguration(TestConfiguration $testConfiguration): self
    {
        if ($this->testConfigurations->removeElement($testConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($testConfiguration->getCampusConfiguration() === $this) {
                $testConfiguration->setCampusConfiguration(null);
            }
        }

        return $this;
    }

    public function getSudokuConfiguration(): ?SudokuConfiguration
    {
        return $this->sudokuConfiguration;
    }

    public function setSudokuConfiguration(?SudokuConfiguration $sudokuConfiguration): self
    {
        $this->sudokuConfiguration = $sudokuConfiguration;

        return $this;
    }
}
