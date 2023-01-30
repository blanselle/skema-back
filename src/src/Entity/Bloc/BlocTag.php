<?php

declare(strict_types=1);

namespace App\Entity\Bloc;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class BlocTag
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read'
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups([
        'bloc:collection:read',
        'bloc:item:read'
    ])]
    private string $label;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): BlocTag
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): BlocTag
    {
        $this->label = $label;

        return $this;
    }
}
