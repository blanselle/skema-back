<?php

declare(strict_types=1);

namespace App\Entity\Admissibility;

use App\Repository\Admissibility\ConversionTableRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversionTableRepository::class)]
#[ORM\Table(name: 'admissibility_conversion_table')]
class ConversionTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'float')]
    private float $score;

    #[ORM\Column(type: 'float')]
    private float $note;

    #[ORM\ManyToOne(targetEntity: Param::class, inversedBy: 'conversionTableResults')]
    #[ORM\JoinColumn(nullable: false)]
    private Param $param;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(float $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getParam(): ?Param
    {
        return $this->param;
    }

    public function setParam(?Param $param): self
    {
        $this->param = $param;

        return $this;
    }
}
