<?php

declare(strict_types=1);

namespace App\Entity\CV\Bac;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\DateTrait;
use App\Repository\CV\Bac\BacDistinctionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BacDistinctionRepository::class)]
#[ApiResource(
    order: ["id" => "ASC"],
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacDistinction:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacDistinction:item:read']]
        ],
    ]
)]
class BacDistinction
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups([
        'bacDistinction:item:read',
        'bacDistinction:collection:read',
    ])]
    private string $label;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $code = null;

    public function __construct()
    {
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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
