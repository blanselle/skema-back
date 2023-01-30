<?php

declare(strict_types=1);

namespace App\Entity\AdministrativeRecord;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use App\Entity\Traits\DateTrait;
use App\Repository\AdministrativeRecord\ScholarShipLevelRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ScholarShipLevelRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['ssl:collection:read'],
            ],
            'security' => 'is_granted("PUBLIC_ACCESS")',
        ],
    ]
)]

#[ApiFilter(OrderFilter::class, properties: ['position'])]

class ScholarShipLevel
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'ssl:collection:read',
        'ar:item:read',
        'ar:collection:read',
        'ar:item:write',
    ])]
    private int $id;

    #[ORM\Column]
    #[Groups([
        'ssl:collection:read',
        'ar:item:read',
        'ar:collection:read',
        'ar:item:write',
    ])]
    private string $label;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'ssl:collection:read',
        'ar:item:read',
        'ar:collection:read',
        'ar:item:write',
    ])]
    private int $position = 0;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): ScholarShipLevel
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): ScholarShipLevel
    {
        $this->label = $label;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): ScholarShipLevel
    {
        $this->position = $position;

        return $this;
    }
}
