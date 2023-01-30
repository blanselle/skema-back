<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Repository\Admissibility\Bonus\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_category')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\Length(max: 180)]
    private string $name;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\Length(max: 180)]
    private string $key;

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

    public function getKey(): string
    {
        return $this->key;
    }
    
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
