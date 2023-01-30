<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Exam\ExamSession;
use App\Entity\OralTest\CampusConfiguration;
use App\Entity\Parameter\Parameter;
use App\Entity\Traits\DateTrait;
use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CampusRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['campus:collection:read'],
            ],
            'order' => ['city' => 'ASC']
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['campus:item:read'],
            ]
        ],
    ],
)]
class Campus
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'session:collection:read',
        'exam_student:sub:read',
        'slot:collection:read',
        'oralTestStudent:collection:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 150, unique: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
        'session:collection:read',
        'exam_student:sub:read',
        'slot:collection:read',
        'oralTestStudent:collection:read',
    ])]
    #[Assert\NotNull]
    private ?string $name = null;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read'
    ])]
    #[Assert\Valid]
    private Media|null $media = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $addressLine1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $addressLine2 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $addressLine3 = null;

    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $postalCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private string $email;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private ?string $phoneReception = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    #[Assert\NotNull]
    private ?string $phoneCustomerService = null;

    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: ExamSession::class, orphanRemoval: true)]
    private Collection $examSessions;

    #[ORM\ManyToMany(targetEntity: Parameter::class, mappedBy: 'campuses')]
    private Collection $parameters;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $instruction = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private bool $assignmentCampus = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    private bool $oralTestCenter = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
    ])]
    #[Assert\Expression(
        "not (this.isOralTestCenter() === true and this.getContestJuryWebsiteCode() === null)",
        message: 'Le code site jury concours est obligatoire.',
    )]
    private ?string $contestJuryWebsiteCode = null;

    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: CampusConfiguration::class)]
    private Collection $campusConfigurations;

    public function __construct()
    {
        $this->examSessions = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->campusConfigurations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): Campus
    {
        $this->media = $media;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    public function setAddressLine3(?string $addressLine3): self
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneReception(): ?string
    {
        return $this->phoneReception;
    }

    public function setPhoneReception(?string $phoneReception): self
    {
        $this->phoneReception = $phoneReception;

        return $this;
    }

    public function getPhoneCustomerService(): ?string
    {
        return $this->phoneCustomerService;
    }

    public function setPhoneCustomerService(string $phoneCustomerService): self
    {
        $this->phoneCustomerService = $phoneCustomerService;

        return $this;
    }

    /**
     * @return Collection<int, ExamSession>
     */
    public function getExamSessions(): Collection
    {
        return $this->examSessions;
    }

    public function addExamSession(ExamSession $examSession): self
    {
        if (!$this->examSessions->contains($examSession)) {
            $this->examSessions[] = $examSession;
            $examSession->setCampus($this);
        }

        return $this;
    }

    public function removeExamSession(ExamSession $examSession): self
    {
        if ($this->examSessions->removeElement($examSession)) {
            // set the owning side to null (unless already changed)
            if ($examSession->getCampus() === $this) {
                $examSession->setCampus(null);
            }
        }

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
            $parameter->addCampus($this);
        }

        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            $parameter->removeCampus($this);
        }

        return $this;
    }

    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    public function setInstruction(?string $instruction): self
    {
        $this->instruction = $instruction;

        return $this;
    }

    public function isAssignmentCampus(): bool
    {
        return $this->assignmentCampus;
    }

    public function setAssignmentCampus(bool $assignmentCampus): self
    {
        $this->assignmentCampus = $assignmentCampus;

        return $this;
    }

    public function isOralTestCenter(): bool
    {
        return $this->oralTestCenter;
    }

    public function setOralTestCenter(bool $oralTestCenter): self
    {
        $this->oralTestCenter = $oralTestCenter;

        return $this;
    }

    public function getContestJuryWebsiteCode(): ?string
    {
        return $this->contestJuryWebsiteCode;
    }

    public function setContestJuryWebsiteCode(?string $contestJuryWebsiteCode): self
    {
        $this->contestJuryWebsiteCode = $contestJuryWebsiteCode;

        return $this;
    }

    /**
     * @return Collection<int, CampusConfiguration>
     */
    public function getCampusConfigurations(): Collection
    {
        return $this->campusConfigurations;
    }

    public function addCampusConfiguration(CampusConfiguration $campusConfiguration): self
    {
        if (!$this->campusConfigurations->contains($campusConfiguration)) {
            $this->campusConfigurations->add($campusConfiguration);
            $campusConfiguration->setCampus($this);
        }

        return $this;
    }

    public function removeCampusConfiguration(CampusConfiguration $campusConfiguration): self
    {
        if ($this->campusConfigurations->removeElement($campusConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($campusConfiguration->getCampus() === $this) {
                $campusConfiguration->setCampus(null);
            }
        }

        return $this;
    }
}
