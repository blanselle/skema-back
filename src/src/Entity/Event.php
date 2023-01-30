<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Parameter\Parameter;
use App\Entity\Traits\DateTrait;
use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['event:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['event:item:read']]
        ],
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['programChannels.id' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['paramStart.valueDateTime','paramEnd.valueDateTime'], arguments: ['orderParameterName' => 'order'])]
class Event
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'event:item:read',
        'event:collection:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        'event:item:read',
        'event:collection:read',
    ])]
    private ?string $label = null;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'event:item:read',
        'event:collection:read',
    ])]
    private ?Parameter $paramStart = null;

    #[ORM\ManyToOne(targetEntity: Parameter::class)]
    #[Groups([
        'event:item:read',
        'event:collection:read',
    ])]
    #[Assert\Expression(
        "null == this.getParamEnd() || this.getParamStart().getValue() < this.getParamEnd().getValue()",
        message:"La date de fin ne doit pas être antérieure à la date de début"
    )]
    private ?Parameter $paramEnd = null;

    #[Groups([
        'event:item:read',
        'event:collection:read',
    ])]
    private string $status;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class, inversedBy: 'events')]
    #[Groups([
        'event:item:read',
        'event:collection:read',
    ])]
    #[Assert\Count(min: 1, minMessage: 'Vous devez renseigner au moins une voie de concours')]
    private Collection $programChannels;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getParamStart(): ?Parameter
    {
        return $this->paramStart;
    }

    public function setParamStart(?Parameter $paramStart): self
    {
        $this->paramStart = $paramStart;

        return $this;
    }

    public function getParamEnd(): ?Parameter
    {
        return $this->paramEnd;
    }

    public function setParamEnd(?Parameter $paramEnd): self
    {
        $this->paramEnd = $paramEnd;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

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
            $this->programChannels->add($programChannel);
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }
}
