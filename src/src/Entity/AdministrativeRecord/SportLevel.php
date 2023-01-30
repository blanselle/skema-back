<?php

declare(strict_types=1);

namespace App\Entity\AdministrativeRecord;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits\DateTrait;
use App\Repository\AdministrativeRecord\SportLevelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SportLevelRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['hlsl:collection:read'],
            ],
            'security' => 'is_granted("PUBLIC_ACCESS")',
        ],
    ]
)]

#[ApiFilter(OrderFilter::class, properties: ['position'])]

class SportLevel
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'hlsl:collection:read',
        'ar:item:read',
        'ar:collection:read',
        'ar:item:write',
    ])]
    private int $id;

    #[ORM\Column]
    #[Groups([
        'hlsl:collection:read',
        'ar:item:read',
        'ar:collection:read',
        'ar:item:write',
    ])]
    private string $label;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'hlsl:collection:read',
        'ar:item:read',
        'ar:collection:read',
        'ar:item:write',
    ])]
    private int $position = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): SportLevel
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): SportLevel
    {
        $this->label = $label;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): SportLevel
    {
        $this->position = $position;

        return $this;
    }
}
