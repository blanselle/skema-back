<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\ProgramChannel;
use App\Entity\Traits\DateTrait;
use App\Repository\Exam\ExamLanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExamLanguageRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['examLanguage:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['examLanguage:item:read']]
        ],
    ],
    attributes: ["security" => "is_granted('ROLE_CANDIDATE')"]
)]
class ExamLanguage
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['examLanguage:collection:read', 'examLanguage:item:read'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 30)]
    #[Groups(['examLanguage:collection:read', 'examLanguage:item:read', 'ar:item:read', 'ar:collection:read'])]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    private string $name;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: 'string', length: 3, nullable: true)]
    private ?string $key = null;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class, inversedBy: 'examLanguages')]
    #[Assert\Count(min: 1, minMessage: 'Vous devez spécifier au moins une voie de concours')]
    private Collection $programChannels;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return Collection<int, ProgramChannel>
     */
    public function getProgramChannels(): Collection
    {
        return $this->programChannels;
    }

    public function addProgramChannel(ProgramChannel $programChannel): self
    {
        if (!$this->programChannels->contains($programChannel)) {
            $this->programChannels[] = $programChannel;
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }
}
