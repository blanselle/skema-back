<?php

declare(strict_types=1);

namespace App\Entity\CV\Bac;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\DateTrait;
use App\Interface\Cv\KeyInterface;
use App\Repository\CV\Bac\BacChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BacChannelRepository::class)]
#[ApiResource(
    order: ["name" => "ASC"],
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacChannel:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacChannel:item:read']]
        ],
    ]
)]

/**
 * Filière du bac. ex : bac General, bac pro
 */
class BacChannel implements KeyInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\Length(
        min: 3,
        minMessage: 'Le nom doit être plus grand que {{ limit }}',
        max: 180,
        maxMessage: 'Le nom doit être plus petit que {{ limit }}',
    )]
    #[Groups([
        'bacChannel:item:read',
        'bacChannel:collection:read',
    ])]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    private ?string $key = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'bacChannel:item:read',
        'bacChannel:collection:read',
    ])]
    private bool $needDetail = false;

    #[ORM\OneToMany(mappedBy: 'bacChannel', targetEntity: BacType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'bacType:item:read',
    ])]
    private Collection $bacTypes;

    public function __construct()
    {
        $this->bacTypes = new ArrayCollection();
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

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * The bac with this bacChannel need detail.
     * The detail is specified in the field : bac.detail
     */
    public function getNeedDetail(): bool
    {
        return $this->needDetail;
    }

    public function setNeedDetail(bool $needDetail): self
    {
        $this->needDetail = $needDetail;

        return $this;
    }

    public function getBacTypes(): Collection
    {
        return $this->bacTypes;
    }

    public function setBacTypes(Collection $bacTypes): self
    {
        $this->bacTypes = $bacTypes;
        foreach($bacTypes as $bacType) {
            if($bacType->getBacChannel() !== $this) {
                $bacType->setBacChannel($this);
            }
        }

        return $this;
    }

    public function addBacType(BacType $bacType): self
    {
        if (!$this->bacTypes->contains($bacType)) {
            $this->bacTypes[] = $bacType;
            $bacType->setBacChannel($this);
        }

        return $this;
    }

    public function removeBacSup(BacType $bacType): self
    {
        $this->bacTypes->removeElement($bacType);

        return $this;
    }
}
