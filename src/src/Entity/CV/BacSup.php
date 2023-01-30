<?php

declare(strict_types=1);

namespace App\Entity\CV;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Constants\CV\BacSupConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Country;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\Loggable\History;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Interface\DetailInterface;
use App\Repository\CV\BacSupRepository;
use App\Validator as Validator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BacSupRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => ['groups' => ['bacSup:collection:read']],
            'denormalization_context' => ['groups' => ['bacSup:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'validation_groups' => ['api'],
            'security_post_denormalize' => 'is_granted("create", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bacSup:item:read']],
            'security' => 'user === object.getCv().getStudent().getUser()'
        ],
        'put' => [
            'normalization_context' => ['groups' => ['bacSup:item:read']],
            'denormalization_context' => ['groups' => ['bacSup:item:write']],
            'security' => 'user === object.getCv().getStudent().getUser()',
            'validation_groups' => ['api'],
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
    ],
    order: ['year' => 'ASC', 'dualPathBacSup' => 'ASC']
)]
#[Assert\Expression(
    'this.getCv().getValidated() === false',
    message: 'Le Cv est déjà validé',
    groups: ['api']
)]
/**
 * Info sur le bac + de l'etudiant
 */
class BacSup implements CvCalculationInterface, DetailInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Diploma::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Assert\NotNull()]
    #[Gedmo\Versioned]
    private ?Diploma $diploma = null;

    #[ORM\ManyToOne(targetEntity: DiplomaChannel::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Expression(
        '(
            this.getDiploma().isDiplomaChannelRequired() === true 
            and this.getDiplomaChannel() in this.getDiploma().getDiplomaChannels().toArray()
        ) or (
            this.getDiploma().isDiplomaChannelRequired() === false
        )',
        message: 'La filière doit faire partie de la liste des filières du diplome',
        groups: ['api', 'bo']
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private ?DiplomaChannel $diplomaChannel = null;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull(groups: ['api', 'bo'])]
    #[Assert\Length(
        min: 1,
        max: 180,
        minMessage: 'Le nom de l\'etablishment doit être plus grand que {{ limit }}',
        maxMessage: 'Le nom de l\'etablishment doit être plus petit que {{ limit }}',
        groups: ['api', 'bo'],
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private string $establishment;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(groups: ['api', 'bo'])]
    #[Assert\Positive(groups: ['api', 'bo'])]
    #[Assert\GreaterThan(999, groups: ['api', 'bo'], message: 'L\'année doit être composée de quatre chiffres')]
    #[Validator\Parameter\LessOrEqualThanParameter(
        parameterName: 'anneeConcours',
        programChannelId: 'this.getCv().getStudent().getProgramChannel().getId()',
        groups: ['api', 'bo'], 
        message: 'Veuillez saisir une année antérieure à {{ parameter }}')
    ]
    #[Validator\Cv\BacSupYear(groups: ['api', 'bo'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private int $year;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotNull(groups: ['api', 'bo'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private string $postalCode;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull(groups: ['api', 'bo'])]
    #[Assert\Length(
        min: 1,
        max: 180,
        minMessage: 'La ville doit être plus grande que {{ limit }}',
        maxMessage: 'La ville doit être plus petit que {{ limit }}',
        groups: ['api', 'bo']
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private string $city;

    #[ORM\ManyToOne(targetEntity: Cv::class, inversedBy: 'bacSups')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Validator\ExpectedUser(
        expression: "object.getStudent().getUser()",
        groups: ['api'],
    )]
    #[Groups([
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    private Cv $cv;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(groups: ['api', 'bo'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private Country $country;

    #[ORM\OneToMany(mappedBy: 'bacSup', targetEntity: SchoolReport::class, cascade: ['persist'], fetch: "EXTRA_LAZY", orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'asc'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Assert\Valid(groups: ['bo'])]
    private Collection $schoolReports;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(
        callback: [BacSupConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
        groups: ['api', 'bo'],
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    #[Gedmo\Versioned]
    private string $type;

    #[ORM\OneToOne(targetEntity: BacSup::class, inversedBy: 'parent', cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:collection:write',
        'bacSup:item:read',
        'bacSup:item:write',
    ])]
    private ?BacSup $dualPathBacSup = null;

    #[ORM\OneToOne(targetEntity: BacSup::class, mappedBy: 'dualPathBacSup')]
    #[Groups([
        'bacSup:collection:write',
        'bacSup:item:write',
    ])]
    private ?BacSup $parent = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:item:read',
        'bacSup:item:write',
        'bacSup:collection:write',
        'bacSup:collection:read',
    ])]
    #[Validator\RequiredField(
        expression: 'this.getDiploma().getNeedDetail() || (this.getDiplomaChannel() !== null && this.getDiplomaChannel().getNeedDetail())',
        nullValues: [null, ''],
        groups: ['api', 'bo', 'cv:validation'],
    )]
    #[Validator\RequiredFieldReverse(
        expression: 'this.getDiploma().getNeedDetail() || (this.getDiplomaChannel() !== null && this.getDiplomaChannel().getNeedDetail())',
        nullValues: [null, ''],
        groups: ['detail-to-null'],
    )]
    #[Gedmo\Versioned]
    private ?string $detail = null;

    private ?string $identifier = null;

    public function __construct()
    {
        $this->schoolReports = new ArrayCollection();
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

    public function getDiploma(): ?Diploma
    {
        return $this->diploma;
    }

    public function setDiploma(?Diploma $diploma): self
    {
        $this->diploma = $diploma;

        return $this;
    }

    public function getDiplomaChannel(): ?DiplomaChannel
    {
        return $this->diplomaChannel;
    }

    public function setDiplomaChannel(?DiplomaChannel $diplomaChannel): BacSup
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

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

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

    public function getCv(): Cv
    {
        return $this->cv;
    }

    public function setCv(Cv $cv): self
    {
        $this->cv = $cv;
        
        if(!$cv->getBacSups()->contains($this)) {
            $cv->addBacSup($this);
        }

        return $this;
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getSchoolReports(): Collection
    {
        return $this->schoolReports;
    }

    public function addSchoolReport(SchoolReport $schoolReport): self
    {
        if (!$this->schoolReports->contains($schoolReport)) {
            $this->schoolReports->add($schoolReport);
            $schoolReport->setBacSup($this);
        }

        return $this;
    }

    public function removeSchoolReport(SchoolReport $schoolReport): self
    {
        if (null !== $schoolReport->getMedia()) {
            $schoolReport->getMedia()->setState(MediaWorflowStateConstants::STATE_CANCELLED);
        }

        $this->schoolReports->removeElement($schoolReport);

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

    public function getDualPathBacSup(): ?BacSup
    {
        return $this->dualPathBacSup;
    }

    public function setDualPathBacSup(?BacSup $dualPathBacSup): BacSup
    {
        $this->dualPathBacSup = $dualPathBacSup;

        if (null === $dualPathBacSup) {
            return $this;
        }

        if($dualPathBacSup->getParent() !== $this) {
            $dualPathBacSup->setParent($this);
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?BacSup $parent): self
    {
        $this->parent = $parent;

        if($parent->getDualPathBacSup() !== $this) {
            $parent->setDualPathBacSup($this);
        }

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): BacSup
    {
        $this->identifier = $identifier;

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
}
