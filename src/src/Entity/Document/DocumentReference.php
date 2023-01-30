<?php

declare(strict_types=1);

namespace App\Entity\Document;

use App\Entity\Traits\DateTrait;
use App\Interface\FileInterface;
use App\Repository\Document\DocumentReferenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DocumentReferenceRepository::class)]
class DocumentReference implements FileInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $file = null;

    public function getId(): ?int
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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFilePath(): string
    {
        return $this->getFile();
    }
}
