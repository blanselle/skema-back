<?php

declare(strict_types=1);

namespace App\Entity\CV\Bac;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Constants\CV\TagBacConstants;
use App\Entity\Traits\DateTrait;
use App\Filter\BacTypeYearFilter;
use App\Repository\CV\Bac\BacTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BacTypeRepository::class)]
#[ApiResource(
    order: ["name" => "ASC"],
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacType:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacType:item:read']]
        ],
    ]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'bacChannel' => 'exact',
])]
#[ApiFilter(BacTypeYearFilter::class)]

/**
 * Type de bac. ex: Math, SI
 */
class BacType
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\Length(
        min: 3,
        max: 180,
        minMessage: 'Le nom doit être plus grand que {{ limit }}',
        maxMessage: 'Le nom doit être plus petit que {{ limit }}',
    )]
    #[Groups([
        'bacType:item:read',
        'bacType:collection:read',
    ])]
    private string $name;

    #[ORM\Column(type: 'array')]
    #[Assert\Choice(
        callback: [TagBacConstants::class, 'getConsts'],
        multiple: true,
        message: 'The tag {{ value }} is not in {{ choices }}',
    )]
    #[Groups([
        'bacType:item:read',
        'bacType:collection:read',
    ])]
    private array $tags = [];

    #[Groups([
        'bacType:collection:read',
    ])]
    #[ORM\ManyToMany(targetEntity: BacOption::class, mappedBy: 'bacTypes')]
    private Collection $bacOptions;

    #[ORM\ManyToOne(targetEntity: BacChannel::class, inversedBy: 'bacTypes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'bacType:item:read',
        'bacType:collection:read',
    ])]
    private BacChannel $bacChannel;

    public function __construct()
    {
        $this->bacOptions = new ArrayCollection();
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

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function getBacOptions(): Collection
    {
        return $this->bacOptions;
    }

    public function addBacOption(BacOption $bacOption): self
    {
        if (!$this->bacOptions->contains($bacOption)) {
            $this->bacOptions[] = $bacOption;
            $bacOption->addBacType($this);
        }

        return $this;
    }

    public function removeBacOption(BacOption $bacOption): self
    {
        if ($this->bacOptions->removeElement($bacOption)) {
            $bacOption->removeBacType($this);
        }

        return $this;
    }

    /**
     * Channel associate with the type of baccalaureate
     * ex: General ==> Scientificique
     */
    public function getBacChannel(): BacChannel
    {
        return $this->bacChannel;
    }

    public function setBacChannel(BacChannel $bacChannel): self
    {
        $this->bacChannel = $bacChannel;

        if(!$bacChannel->getBacTypes()->contains($this)) {
            $bacChannel->addBacType($this);
        }

        return $this;
    }
}
