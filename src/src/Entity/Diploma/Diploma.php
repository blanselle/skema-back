<?php

declare(strict_types=1);

namespace App\Entity\Diploma;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Entity\ProgramChannel;
use App\Entity\Traits\DateTrait;
use App\Repository\Diploma\DiplomaRepository;
use App\Validator\Diploma\CountDiplomaChannel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiplomaRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['diploma:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['diploma:item:read']]
        ],
    ],
    attributes: ["security" => "is_granted('PUBLIC_ACCESS')"],
    order: ["name" => "ASC"]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['name' => 'partial'])]
#[ApiFilter(filterClass: BooleanFilter::class, properties: ['additional' => 'exact'])]
class Diploma
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull()]
    #[Assert\Length(min: 3, max: 180)]
    #[Groups([
        'diploma:collection:read',
        'diploma:item:read',
        'diplomaChannel:collection:read',
        'diplomaChannel:item:read',
        'ar:item:read',
        'ar:collection:read',
    ])]
    private string $name;

    #[ORM\ManyToMany(targetEntity: DiplomaChannel::class, inversedBy: 'diplomas')]
    #[CountDiplomaChannel()]
    #[Groups([
        'diploma:collection:read',
        'diploma:item:read',
    ])]
    private Collection $diplomaChannels;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class, inversedBy: 'diplomas')]
    #[Assert\Count(min: 1, minMessage: 'Vous devez spécifier au moins une voie de concours')]
    #[Groups([
        'diploma:collection:read',
        'diploma:item:read',
    ])]
    private Collection $programChannels;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'diploma:collection:read',
        'diploma:item:read',
        'ar:item:read',
    ])]
    private bool $needDetail = false;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'diploma:collection:read',
        'diploma:item:read',
    ])]
    private bool $additional = false;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    #[Groups([
        'diploma:collection:read',
        'diploma:item:read',
    ])]
    private bool $diplomaChannelRequired = true;

    public function __construct()
    {
        $this->diplomaChannels = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDiplomaChannels(): Collection
    {
        return $this->diplomaChannels;
    }

    public function addDiplomaChannel(DiplomaChannel $diplomaChannel): self
    {
        if (!$this->diplomaChannels->contains($diplomaChannel)) {
            $this->diplomaChannels[] = $diplomaChannel;
        }

        return $this;
    }

    public function removeDiplomaChannel(DiplomaChannel $diplomaChannel): self
    {
        $this->diplomaChannels->removeElement($diplomaChannel);

        return $this;
    }

    public function getProgramChannels(): Collection
    {
        return $this->programChannels;
    }

    public function addProgramChannel(ProgramChannel $programChannel): self
    {
        if (!$this->programChannels->contains($programChannel)) {
            $this->programChannels[] = $programChannel;
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }

    // In the registration step, the user had to enter details if the diplomaChannel.needDetail is true
    public function getNeedDetail(): bool
    {
        return $this->needDetail;
    }

    public function setNeedDetail(bool $needDetail): self
    {
        $this->needDetail = $needDetail;

        return $this;
    }

    public function getAdditional(): bool
    {
        return $this->additional;
    }

    public function setAdditional(bool $additional): self
    {
        $this->additional = $additional;

        return $this;
    }

    public function isDiplomaChannelRequired(): bool
    {
        return $this->diplomaChannelRequired;
    }

    public function setDiplomaChannelRequired(bool $diplomaChannelRequired): Diploma
    {
        $this->diplomaChannelRequired = $diplomaChannelRequired;

        return $this;
    }
}
