<?php

namespace App\Entity\Admissibility;

use App\Repository\Admissibility\CalculatorRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CalculatorRepository::class)]
#[ORM\Table(name: 'admissibility_calculator')]
#[UniqueEntity('type')]
class Calculator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $running = false;

    #[ORM\Column(type: 'string', length: 150)]
    private string $type;

    #[ORM\Column(type: 'uuid', nullable: false)]
    private Uuid $userId;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTimeInterface $lastLaunchDate;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isRunning(): ?bool
    {
        return $this->running;
    }

    public function setRunning(bool $running): self
    {
        $this->running = $running;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->running? 'Calculator is running, please wait before launching Ranking.' :'Calculator is ready to process Ranking.';
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getLastLaunchDate(): ?\DateTimeInterface
    {
        return $this->lastLaunchDate;
    }

    public function setLastLaunchDate(\DateTimeInterface $lastLaunchDate): self
    {
        $this->lastLaunchDate = $lastLaunchDate;

        return $this;
    }
}
