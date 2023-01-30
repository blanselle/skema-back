<?php

declare(strict_types=1);

namespace App\Entity\Admissibility;

use App\Entity\Exam\ExamClassification;
use App\Entity\Traits\DateTrait;
use App\Repository\Admissibility\AdmissibilityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdmissibilityRepository::class)]
class Admissibility
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToMany(mappedBy: 'admissibility', targetEntity: Param::class, orphanRemoval: true)]
    private Collection $params;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    private string $type;

    #[ORM\OneToOne(targetEntity: ExamClassification::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ExamClassification $examClassification;

    private array $admissibilityByProgramChannel = [];

    public function __construct()
    {
        $this->params = new ArrayCollection();
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

    public function setParams(Collection $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return Collection<int, Param>
     */
    public function getParams(): Collection
    {
        return $this->params;
    }

    public function addParam(Param $param): self
    {
        if (!$this->params->contains($param)) {
            $this->params[] = $param;
            $param->setAdmissibility($this);
        }

        return $this;
    }

    public function removeParam(Param $param): self
    {
        if ($this->params->removeElement($param)) {
            // set the owning side to null (unless already changed)
            if ($param->getAdmissibility() === $this) {
                $param->setAdmissibility(null);
            }
        }

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getExamClassification(): ?ExamClassification
    {
        return $this->examClassification;
    }

    public function setExamClassification(ExamClassification $examClassification): self
    {
        $this->examClassification = $examClassification;

        return $this;
    }

    public function getAdmissibilityByProgramChannel(): array
    {
        if (empty($this->admissibilityByProgramChannel)) {
            foreach ($this->getParams() as $param) {
                $this->admissibilityByProgramChannel[$param->getProgramChannel()->getName()] = $param;
            }
        }

        return $this->admissibilityByProgramChannel;
    }
}
