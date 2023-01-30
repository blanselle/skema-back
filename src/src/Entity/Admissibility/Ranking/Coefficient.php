<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Ranking;

use App\Constants\Admissibility\Ranking\CoefficientTypeConstants;
use App\Entity\ProgramChannel;
use App\Repository\Admissibility\Ranking\CoefficientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CoefficientRepository::class)]
#[ORM\Table(name: 'admissibility_coefficient')]
class Coefficient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(
        callback: [CoefficientTypeConstants::class, 'getConsts'],
        message: 'Type incorrect, il doit être égal à l\'une des valeurs suivantes : {{ choices }}',
    )]
    #[Assert\NotNull]
    private string $type;

    #[ORM\Column(type: 'integer')]
    private int $coefficient;

    #[ORM\ManyToOne(targetEntity: ProgramChannel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ProgramChannel $programChannel;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCoefficient(): int
    {
        return $this->coefficient;
    }

    public function setCoefficient(int $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    public function getProgramChannel(): ProgramChannel
    {
        return $this->programChannel;
    }

    public function setProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannel = $programChannel;

        return $this;
    }
}
