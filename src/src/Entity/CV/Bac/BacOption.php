<?php

declare(strict_types=1);

namespace App\Entity\CV\Bac;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits\DateTrait;
use App\Repository\CV\Bac\BacOptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BacOptionRepository::class)]
#[ApiResource(
    order: ["name" => "ASC"],
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacOption:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacOption:item:read']]
        ],
    ]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'BacType' => 'exact'
])]

/**
 * Option of the baccalaureate. ex: music theatre
 */
class BacOption
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
        'bacOption:item:read',
        'bacOption:collection:read',
        'bacType:collection:read',
    ])]
    private string $name;

    #[ORM\ManyToMany(targetEntity: BacType::class, inversedBy: 'bacOptions')]
    #[Groups([
        'bacOption:item:read',
        'bacOption:collection:read',
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

    /**
     * BacType associate with the option of baccalaureate
     * ex: Scientifique option math
     */
    public function getBacTypes(): Collection
    {
        return $this->bacTypes;
    }

    public function addBacType(BacType $bacType): self
    {
        if (!$this->bacTypes->contains($bacType)) {
            $this->bacTypes[] = $bacType;
        }

        return $this;
    }

    public function removeBacType(BacType $bacType): self
    {
        $this->bacTypes->removeElement($bacType);

        return $this;
    }
}
