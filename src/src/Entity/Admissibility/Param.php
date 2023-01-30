<?php

declare(strict_types=1);

namespace App\Entity\Admissibility;

use App\Entity\ProgramChannel;
use App\Repository\Admissibility\ParamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParamRepository::class)]
#[ORM\Table(name: 'admissibility_param')]
class Param
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $highClipping = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lowClipping = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $median = null;

    #[ORM\ManyToOne(targetEntity: ProgramChannel::class, inversedBy: 'admissibilityParams')]
    #[ORM\JoinColumn(nullable: false)]
    private ProgramChannel $programChannel;

    #[ORM\OneToMany(mappedBy: 'param', targetEntity: Border::class, orphanRemoval: true)]
    private Collection $borders;

    #[ORM\OneToMany(mappedBy: 'param', targetEntity: ConversionTable::class, orphanRemoval: true)]
    private Collection $conversionTableResults;

    #[ORM\ManyToOne(targetEntity: Admissibility::class, inversedBy: 'params')]
    #[ORM\JoinColumn(nullable: false)]
    private Admissibility $admissibility;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $file;

    public function __construct()
    {
        $this->borders = new ArrayCollection();
        $this->conversionTableResults = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getHighClipping(): ?int
    {
        return $this->highClipping;
    }

    public function setHighClipping(?int $highClipping): self
    {
        $this->highClipping = $highClipping;

        return $this;
    }

    public function getLowClipping(): ?int
    {
        return $this->lowClipping;
    }

    public function setLowClipping(?int $lowClipping): self
    {
        $this->lowClipping = $lowClipping;

        return $this;
    }

    public function getMedian(): ?float
    {
        return $this->median;
    }

    public function setMedian(?float $median): self
    {
        $this->median = $median;

        return $this;
    }

    public function getProgramChannel(): ?ProgramChannel
    {
        return $this->programChannel;
    }

    public function setProgramChannel(?ProgramChannel $programChannel): self
    {
        $this->programChannel = $programChannel;

        return $this;
    }

    public function getBorders(): Collection
    {
        $orderedBorders = $this->borders->toArray();
        usort($orderedBorders, function($a, $b) {
            if ($a->getScore() == $b->getScore()) {
                return 0;
            }

            return $a->getScore() < $b->getScore() ? -1 : 1;
        });


        return new ArrayCollection($orderedBorders);
    }

    public function addBorder(Border $border): self
    {
        if (!$this->borders->contains($border)) {
            $this->borders[] = $border;
            $border->setParam($this);
        }

        return $this;
    }

    public function removeBorder(Border $border): self
    {
        if ($this->borders->removeElement($border)) {
            // set the owning side to null (unless already changed)
            if ($border->getParam() === $this) {
                $border->setParam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ConversionTable>
     */
    public function getConversionTableResults(): Collection
    {
        return $this->conversionTableResults;
    }

    public function addConversionTableResult(ConversionTable $conversionTableResult): self
    {
        if (!$this->conversionTableResults->contains($conversionTableResult)) {
            $this->conversionTableResults[] = $conversionTableResult;
            $conversionTableResult->setParam($this);
        }

        return $this;
    }

    public function removeConversionTableResult(ConversionTable $conversionTableResult): self
    {
        if ($this->conversionTableResults->removeElement($conversionTableResult)) {
            // set the owning side to null (unless already changed)
            if ($conversionTableResult->getParam() === $this) {
                $conversionTableResult->setParam(null);
            }
        }

        return $this;
    }

    public function getAdmissibility(): ?Admissibility
    {
        return $this->admissibility;
    }

    public function setAdmissibility(?Admissibility $admissibility): self
    {
        $this->admissibility = $admissibility;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
