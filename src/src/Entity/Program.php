<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProgramRepository;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProgramRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['program:collection:read'],
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['program:item:read'],
            ]
        ],
    ],
)]
class Program
{
    use Traits\DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'program:item:read',
        'program:collection:read',
        'user:item:read',
        'user:collection:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Groups([
        'program:item:read',
        'program:collection:read',
        'user:item:read',
        'user:collection:read',
    ])]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'program', targetEntity: ProgramChannel::class)]
    private Collection $programChannels;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
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
            $this->programChannels[] = $programChannel;
            $programChannel->setProgram($this);
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        if ($this->programChannels->removeElement($programChannel) && $programChannel->getProgram() === $this) {
            $programChannel->setProgram(null);
        }

        return $this;
    }
}
