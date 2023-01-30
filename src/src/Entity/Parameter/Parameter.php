<?php

declare(strict_types=1);

namespace App\Entity\Parameter;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Campus;
use App\Entity\ProgramChannel;
use App\Repository\Parameter\ParameterRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Traits\DateTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ParameterRepository::class)]
#[ORM\Table(name: 'parameters')]
#[ApiFilter(filterClass: SearchFilter::class, properties: ['key.name' => 'partial', 'campus' => 'exact', 'programChannel' => 'exact'])]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['parameter:collection:read']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['parameter:item:read']]
        ],
    ],
    attributes: ["security" => "is_granted('PUBLIC_ACCESS')"]
)]
class Parameter
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: ParameterKey::class, inversedBy: 'parameters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private ParameterKey $key;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $valueString = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $valueDateTime = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $valueNumber = null;

    #[Groups([
        'parameter:collection:read',
        'parameter:item:read',
        'event:item:read',
        'event:collection:read'
    ])]
    private mixed $value = null;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class, inversedBy: 'parameters')]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    #[Assert\Count(min: 1, minMessage: "Vous devez sélectionner au moins une voie de concours")]
    private Collection $programChannels;

    #[ORM\ManyToMany(targetEntity: Campus::class, inversedBy: 'parameters')]
    #[Groups(['parameter:collection:read', 'parameter:item:read'])]
    private Collection $campuses;


    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
        $this->campuses = new ArrayCollection();
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

    public function getKey(): ParameterKey
    {
        return $this->key;
    }

    public function setKey(ParameterKey $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getValueString(): ?string
    {
        return $this->valueString;
    }

    public function setValueString(?string $valueString): self
    {
        $this->valueString = $valueString;

        return $this;
    }

    public function getValueDateTime(): ?DateTimeInterface
    {
        return $this->valueDateTime;
    }

    public function setValueDateTime(?DateTimeInterface $valueDateTime): self
    {
        $this->valueDateTime = $valueDateTime;

        return $this;
    }

    public function getValueNumber(): ?int
    {
        return $this->valueNumber;
    }

    public function setValueNumber(?int $valueNumber): self
    {
        $this->valueNumber = $valueNumber;

        return $this;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setProgramChannels(Collection $programChannels): self
    {
        $this->programChannels = $programChannels;

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

    public function setCampuses(Collection $campuses): self
    {
        $this->campuses = $campuses;

        return $this;
    }

    /**
     * @return Collection<int, Campus>
     */
    public function getCampuses(): Collection
    {
        return $this->campuses;
    }

    public function addCampus(Campus $campus): self
    {
        if (!$this->campuses->contains($campus)) {
            $this->campuses[] = $campus;
        }

        return $this;
    }

    public function removeCampus(Campus $campus): self
    {
        $this->campuses->removeElement($campus);

        return $this;
    }
}
