<?php

declare(strict_types=1);

namespace App\Entity\Diploma;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits\DateTrait;
use App\Repository\Diploma\DiplomaChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiplomaChannelRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['diplomaChannel:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['diplomaChannel:item:read']]
        ],
    ],
    attributes: ["security" => "is_granted('PUBLIC_ACCESS')"],
    order: ["name" => "ASC"],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'name' => 'partial',
])]
class DiplomaChannel
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
        'diploma:item:read',
        'diploma:collection:read',
        'diplomaChannel:item:read',
        'diplomaChannel:collection:read',
        'ar:item:read',
        'ar:collection:read',
    ])]
    private string $name;

    #[ORM\ManyToMany(targetEntity: Diploma::class, mappedBy: 'diplomaChannels')]
    #[Groups([
        'diplomaChannel:collection:read',
        'diplomaChannel:item:read',
    ])]
    private Collection $diplomas;

    /**
     * In the registration step, the user had to enter details if the diplomaChannel.needDetail will be true
     */
    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'diploma:item:read',
        'diploma:collection:read',
        'diplomaChannel:item:read',
        'diplomaChannel:collection:read',
        'ar:item:read',
    ])]
    private bool $needDetail = false;

    public function __construct()
    {
        $this->diplomas = new ArrayCollection();
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
     * @return Collection<int, Diploma>
     */
    public function getDiplomas(): Collection
    {
        return $this->diplomas;
    }

    public function addDiploma(Diploma $diploma): self
    {
        if (!$this->diplomas->contains($diploma)) {
            $this->diplomas[] = $diploma;
            $diploma->addDiplomaChannel($this);
        }

        return $this;
    }

    public function removeDiploma(Diploma $diploma): self
    {
        if ($this->diplomas->removeElement($diploma)) {
            $diploma->removeDiplomaChannel($this);
        }

        return $this;
    }

    public function getNeedDetail(): bool
    {
        return $this->needDetail;
    }

    public function setNeedDetail(bool $needDetail): self
    {
        $this->needDetail = $needDetail;

        return $this;
    }
}
