<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\DistributionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DistributionTypeRepository::class)]
#[ORM\Table(name: 'oral_test_distribution_type')]
class DistributionType
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\Column(length: 50, options: ["comment" => "day or slot"])]
    private ?string $code = null;

    #[ORM\Column(options: ["comment" => "1: day, 2: slot"])]
    private ?int $position = null;

    #[ORM\OneToMany(mappedBy: 'distribution', targetEntity: CampusConfiguration::class)]
    private Collection $campusConfigurations;

    public function __construct()
    {
        $this->campusConfigurations = new ArrayCollection();
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
            $campusConfiguration->setDistribution($this);
        }

        return $this;
    }

    public function removeCampusConfiguration(CampusConfiguration $campusConfiguration): self
    {
        if ($this->campusConfigurations->removeElement($campusConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($campusConfiguration->getDistribution() === $this) {
                $campusConfiguration->setDistribution(null);
            }
        }

        return $this;
    }
}
