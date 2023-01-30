<?php

declare(strict_types=1);

namespace App\Entity\Diploma;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Interface\DetailInterface;
use App\Repository\Diploma\StudentDiplomaRepository;
use App\Validator\Parameter\LessOrEqualThanParameter;
use App\Validator\RequiredField;
use App\Validator\RequiredFieldReverse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => [
                'groups' => ['sd:collection:write'],
            ],
            'validation_groups' => ['sd:validation'],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'security_post_denormalize' => ' is_granted("create", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['sd:item:read'],
            ],
            'security' => 'null !== object.getAdministrativeRecord() and object.getAdministrativeRecord().getStudent().getUser() == user',
        ],
        'put' => [
            'normalization_context' => [
                'groups' => ['sd:item:write'],
            ],
            'validation_groups' => ['sd:validation'],
            'security' => 'null !== object.getAdministrativeRecord() and object.getAdministrativeRecord().getStudent().getUser() == user',
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
    ],
)]

#[ORM\Entity(repositoryClass: StudentDiplomaRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
class StudentDiploma implements DetailInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(groups: ['api', 'bo', 'user:validation', 'sd:validation'])]
    #[Assert\Positive(groups: ['api', 'bo', 'user:validation'])]
    #[Assert\GreaterThan(999, groups: ['api', 'bo', 'user:validation', 'sd:validation'], message: 'L\'année doit être composée de quatre chiffres')]
    #[LessOrEqualThanParameter(
        parameterName: 'anneeConcours',
        programChannelId: 'this.getAdministrativeRecord().getStudent().getProgramChannel().getId()',
        groups: ['user:validation', 'bo'],
        message: 'Veuillez saisir une année antérieure à {{ parameter }}'
    )]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:collection:write',
        'sd:item:write',
    ])]
    #[Gedmo\Versioned]
    private int $year;

    #[ORM\ManyToOne(targetEntity: DiplomaChannel::class)]
    #[Assert\Expression(
        'this.getDiplomaChannel() === null || this.getDiplomaChannel() in this.getDiploma().getDiplomaChannels().toArray()',
        message: 'La filière doit faire partie de la liste des filière du diplome',
        groups: ['user:validation', 'sd:validation'],
    )]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:collection:write',
        'sd:item:write',
    ])]
    #[Gedmo\Versioned]
    private ?DiplomaChannel $diplomaChannel = null;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull(groups: ['user:validation', 'sd:validation'])]
    #[Assert\Length(
        min: 1,
        max: 180,
        minMessage: 'Le nom de l\'etablishment doit être plus grand que {{ limit }}',
        maxMessage: 'Le nom de l\'etablishment doit être plus petit que {{ limit }}',
        groups: ['user:validation', 'sd:validation'],
    )]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:collection:write',
        'sd:item:write',
        'sd:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $establishment;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotNull(groups: ['user:validation', 'sd:validation'])]
    #[Assert\NotBlank(groups: ['user:validation', 'sd:validation'])]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $postalCode;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull(groups: ['user:validation', 'sd:validation'])]
    #[Assert\Length(
        min: 1,
        max: 180,
        minMessage: 'La ville doit être plus grande que {{ limit }}',
        maxMessage: 'La ville doit être plus petit que {{ limit }}',
        groups: ['user:validation', 'sd:validation'],
    )]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $city;

    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]    
    #[RequiredField(
        expression: '
            (this.getDiplomaChannel() !== null && this.getDiplomaChannel().getNeedDetail()) || 
            (this.getDiploma() !== null && this.getDiploma().getNeedDetail())',
        nullValues: [null, ''],
        groups: ['user:validation', 'bo', 'cv:validation'],
    )]
    #[RequiredFieldReverse(
        expression: '
            (this.getDiplomaChannel() !== null && this.getDiplomaChannel().getNeedDetail()) || 
            (this.getDiploma() !== null && this.getDiploma().getNeedDetail())',
        nullValues: [null, ''],
        groups: ['detail-to-null'],
    )]
    #[Gedmo\Versioned]
    private ?string $detail = null;

    #[ORM\ManyToOne(targetEntity: Diploma::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]
    #[Gedmo\Versioned]
    private ?Diploma $diploma = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]
    #[Gedmo\Versioned]
    private bool $lastDiploma = false;

    #[ORM\ManyToOne(targetEntity: AdministrativeRecord::class, inversedBy: 'studentDiplomas')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull(groups: ['user:validation', 'sd:validation'])]
    #[Groups([
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]
    private ?AdministrativeRecord $administrativeRecord = null;

    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ["persist"])]
    #[Groups([
        'user:collection:write',
        'sd:item:read',
        'sd:collection:read',
        'sd:item:write',
        'sd:collection:write',
        'ar:item:read',
        'ar:collection:read',
    ])]
    #[Assert\Valid(groups: ['user:validation', 'sd:validation'])]
    private Collection $diplomaMedias;

    #[ORM\OneToOne(targetEntity: StudentDiploma::class, orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'user:collection:write',
        'ar:item:read',
        'ar:collection:read',
        'sd:item:read',
        'sd:item:write',
        'sd:collection:write',
    ])]
    #[Gedmo\Versioned]
    private ?StudentDiploma $dualPathStudentDiploma = null;

    public function __construct()
    {
        $this->diplomaMedias = new ArrayCollection();
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

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getDiplomaChannel(): ?DiplomaChannel
    {
        return $this->diplomaChannel;
    }

    public function setDiplomaChannel(?DiplomaChannel $diplomaChannel): self
    {
        $this->diplomaChannel = $diplomaChannel;

        return $this;
    }

    public function getEstablishment(): string
    {
        return $this->establishment;
    }

    public function setEstablishment(string $establishment): self
    {
        $this->establishment = $establishment;

        return $this;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getDiploma(): ?Diploma
    {
        return $this->diploma;
    }

    public function setDiploma(Diploma $diploma): self
    {
        $this->diploma = $diploma;

        return $this;
    }

    public function getLastDiploma(): bool
    {
        return $this->lastDiploma;
    }

    public function setLastDiploma(bool $lastDiploma): self
    {
        $this->lastDiploma = $lastDiploma;

        return $this;
    }

    public function getAdministrativeRecord(): ?AdministrativeRecord
    {
        return $this->administrativeRecord;
    }

    public function setAdministrativeRecord(AdministrativeRecord $administrativeRecord): StudentDiploma
    {
        $this->administrativeRecord = $administrativeRecord;

        return $this;
    }

    public function getDiplomaMedias(): Collection
    {
        return $this->diplomaMedias;
    }

    public function addDiplomaMedia(Media $diplomaMedia): self
    {
        if (!$this->diplomaMedias->contains($diplomaMedia)) {
            $this->diplomaMedias->add($diplomaMedia);
        }

        return $this;
    }

    public function removeDiplomaMedia(Media $diplomaMedia): self
    {
        $diplomaMedia->setState(MediaWorflowStateConstants::STATE_CANCELLED);
        $this->diplomaMedias->removeElement($diplomaMedia);

        return $this;
    }

    public function getDualPathStudentDiploma(): ?StudentDiploma
    {
        return $this->dualPathStudentDiploma;
    }

    public function setDualPathStudentDiploma(?StudentDiploma $dualPathStudentDiploma): StudentDiploma
    {
        $this->dualPathStudentDiploma = $dualPathStudentDiploma;

        return $this;
    }
}
