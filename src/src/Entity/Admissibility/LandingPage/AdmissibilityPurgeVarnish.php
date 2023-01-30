<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\LandingPage;

use App\Entity\ProgramChannel;
use App\Repository\Admissibility\LandingPage\AdmissibilityPurgeVarnishRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdmissibilityPurgeVarnishRepository::class)]
class AdmissibilityPurgeVarnish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ProgramChannel::class)]
    private ProgramChannel $programChannel;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $state = false;    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

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

    public function getState(): bool
    {
        return $this->state;
    }

    public function setState(bool $state): self
    {
        $this->state = $state;
     
        return $this;
    }
}