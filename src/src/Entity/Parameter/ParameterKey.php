<?php

declare(strict_types=1);

namespace App\Entity\Parameter;

use App\Constants\Parameters\ParametersKeyTypeConstants;
use App\Entity\Traits\DateTrait;
use App\Repository\Parameter\ParameterKeyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParameterKeyRepository::class)]
class ParameterKey
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom doit faire au minimum {{ limit }} caractères',
        maxMessage: 'Le nom doit faire au maximum {{ limit }} caractères',
    )]
    #[ORM\Column(type: 'string', length: 100)]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private string $name;

    #[Assert\Choice(
        callback: [ParametersKeyTypeConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
    )]
    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private string $type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private ?string $format = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'key', targetEntity: Parameter::class)]
    /**
     * @var Collection<int, Parameter>
     */
    private Collection $parameters;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Parameter>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->setKey($this);
        }

        return $this;
    }
}
