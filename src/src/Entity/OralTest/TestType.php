<?php

namespace App\Entity\OralTest;

use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\TestTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestTypeRepository::class)]
#[ORM\Table(name: 'oral_test_test_type')]
class TestType
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $label = null;

    #[ORM\Column(length: 50, options: ["comment" => "ent or lang"])]
    private ?string $code = null;

    #[ORM\Column(options: ["comment" => "1: ent, 2: lang"])]
    private ?int $position = null;

    #[ORM\OneToMany(mappedBy: 'testType', targetEntity: TestConfiguration::class)]
    private Collection $testConfigurations;

    public function __construct()
    {
        $this->testConfigurations = new ArrayCollection();
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
            $testConfiguration->setTestType($this);
        }

        return $this;
    }

    public function removeTestConfiguration(TestConfiguration $testConfiguration): self
    {
        if ($this->testConfigurations->removeElement($testConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($testConfiguration->getTestType() === $this) {
                $testConfiguration->setTestType(null);
            }
        }

        return $this;
    }
}
