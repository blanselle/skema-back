<?php

declare(strict_types=1);

namespace App\Entity\CV;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CV\LanguageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['language:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['language:item:read']]
        ],
    ],
    attributes: ["pagination_client_enabled" => true],
    order: ["code" => "ASC"]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'code' => 'exact',
])]

/**
 * Language disponible pour le CV
 */
class Language
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 3)]
    #[Groups([
        'language:collection:read',
        'language:item:read',
    ])]
    #[Assert\Length(exactly: 3)]
    private string $code;

    #[ORM\Column(type: 'string', length: 180)]
    #[Groups([
        'language:collection:read',
        'language:item:read',
    ])]
    private string $label;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Identifiant de la langue
     */
    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
}
