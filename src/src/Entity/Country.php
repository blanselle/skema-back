<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits\DateTrait;
use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'idCountry' => 'exact',
    'codeSISE' => 'exact',
    'name' => 'partial',
    'nationality' => 'partial',
    'active' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'nationality'])]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['country:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['country:item:read']]
        ],
    ],
    attributes: ["security" => "is_granted('PUBLIC_ACCESS')", "pagination_enabled" => false],
)]
class Country
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 10)]
    #[Groups(['country:collection:read', 'country:item:read'])]
    private string $idCountry;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 180)]
    #[Groups(['country:collection:read', 'country:item:read'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    #[Assert\Length(max: 180)]
    #[Groups(['country:collection:read', 'country:item:read'])]
    private ?string $nationality = null;

    #[ORM\Column(type: 'string', length: 10)]
    #[Assert\NotNull()]
    #[Assert\Length(max: 10)]
    #[Groups(['country:collection:read', 'country:item:read'])]
    private string $codeSISE;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotNull()]
    #[Groups(['country:collection:read', 'country:item:read'])]
    private bool $active = true;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getIdCountry(): string
    {
        return $this->idCountry;
    }

    public function setIdCountry(string $idCountry): self
    {
        $this->idCountry = strtoupper($idCountry);

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

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(?string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getCodeSISE(): string
    {
        return $this->codeSISE;
    }

    public function setCodeSISE(string $codeSISE): self
    {
        $this->codeSISE = strtoupper($codeSISE);

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
