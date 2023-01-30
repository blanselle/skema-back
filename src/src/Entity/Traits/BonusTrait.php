<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\Admissibility\Bonus\Category;
use App\Entity\ProgramChannel;
use Doctrine\ORM\Mapping as ORM;
use ReflectionClass;
use Symfony\Component\Validator\Constraints as Assert;

trait BonusTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'float')]
    #[Assert\PositiveOrZero(message: 'La note ne peut pas être négative.')]
    #[Assert\LessThanOrEqual(20, message: 'La note ne peut pas être superieure à 20.')]
    private float $value;

    #[ORM\ManyToOne(targetEntity: ProgramChannel::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull()]
    private ProgramChannel $programChannel;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull()]
    private Category $category;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getProgramChannel(): ProgramChannel
    {
        return $this->programChannel;
    }

    public function setProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannel = $programChannel;

        return $this;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getClass(): string
    {
        return (new ReflectionClass(self::class))->getShortName();
    }
}
