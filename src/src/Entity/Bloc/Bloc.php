<?php

declare(strict_types=1);

namespace App\Entity\Bloc;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\ProgramChannel;
use App\Entity\Media;
use App\Entity\Traits\DateTrait;
use App\Repository\BlocRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BlocRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['bloc:collection:read'],
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['bloc:item:read'],
            ],
        ],
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['tag.label' => 'exact', 'key' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['position'], arguments: ['orderParameterName' => 'order'])]
class Bloc
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private ?string $key;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private ?string $label = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private ?string $content;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private string $link;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private string $labelLink;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private int $position = 0;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private bool $active = true;

    #[ORM\ManyToOne(targetEntity: BlocTag::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    #[Assert\NotNull()]
    private BlocTag $tag;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    #[Assert\Valid]
    private Media|null $media = null;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    #[Assert\Count(min: 1, minMessage: "Vous devez sélectionner au moins une voie de concours")]
    private Collection $programChannels;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Bloc
    {
        $this->id = $id;
        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): Bloc
    {
        $this->label = $label;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): Bloc
    {
        $this->content = $content;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): Bloc
    {
        $this->link = $link;
        return $this;
    }

    public function getLabelLink(): string
    {
        return $this->labelLink;
    }

    public function setLabelLink(string $labelLink): Bloc
    {
        $this->labelLink = $labelLink;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): Bloc
    {
        $this->position = $position;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): Bloc
    {
        $this->active = $active;
        return $this;
    }

    public function getTag(): BlocTag
    {
        return $this->tag;
    }

    public function setTag(BlocTag $tag): Bloc
    {
        $this->tag = $tag;
        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): Bloc
    {
        $this->media = $media;

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
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }
}
