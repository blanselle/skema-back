<?php

declare(strict_types=1);

namespace App\Entity\AdministrativeRecord;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\AdministrativeRecord\ArAssertsValidationController;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\CV\Cv;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Exam\ExamLanguage;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Entity\Student;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Validator as Validator;
use App\Validator\AdministrativeRecord\HighLevelSportsmanMediasConstraint;
use App\Validator\AdministrativeRecord\LastDiplomaConstraint;
use App\Validator\AdministrativeRecord\ScholarShipMediasConstraint;
use App\Validator\AdministrativeRecord\SportLevelConstraint;
use App\Validator\AdministrativeRecord\StudentConstraint;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['ar:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['ar:item:read']],
            'denormalization_context' => ['groups' => ['ar:item:write']],
            'validation_groups' => ['ar:item:write'],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user',
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
        'validate_asserts' => [
            'method' => 'POST',
            'path' => '/administrative_records/{id}/completed',
            'controller' => ArAssertsValidationController::class,
            'validation_groups' => ['ar:validated'],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user',
        ]
    ],
)]
class AdministrativeRecord implements CvCalculationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'ar:item:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Assert\NotNull(groups: ['ar:validated'])]
    #[Gedmo\Versioned]
    private ?bool $optionalExamLanguage = null;

    #[ORM\ManyToOne(targetEntity: ExamLanguage::class)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Validator\RequiredField(
        expression: 'this.getOptionalExamLanguage() === true',
        message: 'Le choix de la langue doit être précisé',
        nullValues: [null, ''],
        groups: ['ar:validated', 'bo'],
    )]
    #[Gedmo\Versioned]
    private ?ExamLanguage $examLanguage = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Assert\NotNull(groups: ['ar:validated'])]
    #[Gedmo\Versioned]
    private ?bool $highLevelSportsman = null;

    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ["persist"])]
    #[ORM\JoinTable(name:"high_level_sportsman_medias")]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[HighLevelSportsmanMediasConstraint(groups: ['ar:validated'])]
    #[Assert\Valid(groups: ['ar:validated'])]
    private Collection $highLevelSportsmanMedias;

    #[ORM\ManyToOne(targetEntity: SportLevel::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[SportLevelConstraint]
    #[Validator\RequiredField(
        expression: 'this.getHighLevelSportsman()',
        message: 'Le niveau de sport doit être précisé',
        groups: ['ar:validated'],
    )]
    #[Gedmo\Versioned]
    private ?SportLevel $sportLevel;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Assert\NotNull(groups: ['ar:validated'])]
    #[Assert\Expression(
        "this.isThirdTimeMediasApproved() === false || this.isThirdTimeMediasApproved() === true && this.getThirdTime() === true",
        message: 'Un document de tiers temps ayant été validé, le champ doit être à « Oui »',
        groups: ['ar:validated', 'ar:item:write'],
    )]
    #[Assert\Expression(
        "this.getThirdTimeNeedDetail() !== true || this.getThirdTimeNeedDetail() === true && this.getThirdTime() === true",
        message: 'Si « autre aménagement » tiers temps est activé, le champ doit être à « Oui »"',
        groups: ['ar:validated', 'ar:item:write'],
    )]
    #[Gedmo\Versioned]
    private ?bool $thirdTime = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $thirdTimeNeedDetail = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le détail ne doit pas excéder {{ limit }} caractères',
        groups: ['ar:validated', 'ar:item:write']
    )]
    #[Validator\RequiredField(
        expression: 'this.getThirdTimeNeedDetail()',
        message: 'Si « autre aménagement » tiers temps est activé, le détail doit être précisé',
        nullValues: [null, ''],
        groups: ['ar:validated', 'ar:item:write'],
    )]
    #[Gedmo\Versioned]
    private ?string $thirdTimeDetail = null;

    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ["persist"])]
    #[ORM\JoinTable(name:"third_time_medias")]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Assert\Valid(groups: ['ar:validated'])]
    private Collection $thirdTimeMedias;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Assert\NotNull(groups: ['ar:validated'])]
    #[Gedmo\Versioned]
    private ?bool $scholarShip = null;

    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ["persist"])]
    #[ORM\JoinTable(name:"scholar_ship_medias")]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[ScholarShipMediasConstraint(groups: ['ar:validated'])]
    #[Assert\Valid(groups: ['ar:validated'])]
    private Collection $scholarShipMedias;

    #[ORM\ManyToOne(targetEntity: ScholarShipLevel::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Validator\RequiredField(
        expression: 'this.getScholarShip()',
        message: 'Le niveau boursier doit être précisé',
        groups: ['ar:validated'],
    )]
    #[Gedmo\Versioned]
    private ?ScholarShipLevel $scholarShipLevel;

    #[ORM\ManyToMany(targetEntity: Media::class, cascade: ["persist"])]
    #[ORM\JoinTable(name:"id_card_medias")]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    #[Assert\Valid(groups: ['ar:validated'])]
    private Collection $idCards;

    #[ORM\OneToMany(mappedBy: 'administrativeRecord', targetEntity: StudentDiploma::class, cascade: ["persist", 'remove'])]
    #[ORM\OrderBy(["id" => "asc"])]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
        'user:collection:write',
    ])]
    #[Assert\Valid(groups: ['user:validation', 'bo'])]
    #[LastDiplomaConstraint(groups: ['ar:validated', 'bo'])]
    private Collection $studentDiplomas;

    #[ORM\OneToOne(inversedBy: 'administrativeRecord', targetEntity: Student::class, cascade: ['persist'])]
    #[LastDiplomaConstraint(groups: ['ar:validated', 'ar:item:write'])]
    #[StudentConstraint(groups: ['ar:validated', 'ar:item:write'])]
    private ?Student $student = null;

    #[Groups([
        'ar:item:read',
        'ar:collection:read',
    ])]
    private ?StudentDiploma $studentLastDiploma = null;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'ar:item:read',
        'ar:item:write',
    ])]
    private ?Media $jdc = null;  // Attestation Journée Défense et Citoyenneté

    public function __construct()
    {
        $this->highLevelSportsmanMedias = new ArrayCollection();
        $this->thirdTimeMedias = new ArrayCollection();
        $this->scholarShipMedias = new ArrayCollection();
        $this->idCards = new ArrayCollection();
        $this->studentDiplomas = new ArrayCollection();
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

    public function getOptionalExamLanguage(): ?bool
    {
        return $this->optionalExamLanguage;
    }

    public function setOptionalExamLanguage(?bool $optionalExamLanguage): self
    {
        $this->optionalExamLanguage = $optionalExamLanguage;

        return $this;
    }

    public function getExamLanguage(): ?ExamLanguage
    {
        return $this->examLanguage;
    }

    public function setExamLanguage(?ExamLanguage $examLanguage): self
    {
        $this->examLanguage = $examLanguage;

        return $this;
    }

    public function getHighLevelSportsman(): ?bool
    {
        return $this->highLevelSportsman;
    }

    public function setHighLevelSportsman(?bool $highLevelSportsman): self
    {
        $this->highLevelSportsman = $highLevelSportsman;

        return $this;
    }

    public function getHighLevelSportsmanMedias(): Collection
    {
        return $this->highLevelSportsmanMedias;
    }

    public function addHighLevelSportsmanMedia(Media $highLevelSportsmanMedia): self
    {
        if (!$this->highLevelSportsmanMedias->contains($highLevelSportsmanMedia)) {
            $this->highLevelSportsmanMedias[] = $highLevelSportsmanMedia;
        }

        return $this;
    }

    public function getSportLevel(): ?SportLevel
    {
        return $this->sportLevel;
    }

    public function setSportLevel(?SportLevel $sportLevel): self
    {
        $this->sportLevel = $sportLevel;

        return $this;
    }

    public function removeHighLevelSportsmanMedia(Media $highLevelSportsmanMedia): self
    {
        $highLevelSportsmanMedia->setState(MediaWorflowStateConstants::STATE_CANCELLED);
        $this->highLevelSportsmanMedias->removeElement($highLevelSportsmanMedia);

        return $this;
    }

    public function getThirdTime(): ?bool
    {
        return $this->thirdTime;
    }

    public function setThirdTime(?bool $thirdTime): self
    {
        $this->thirdTime = $thirdTime;

        return $this;
    }

    public function getThirdTimeNeedDetail(): ?bool
    {
        return $this->thirdTimeNeedDetail;
    }

    public function setThirdTimeNeedDetail(?bool $thirdTimeNeedDetail): self
    {
        $this->thirdTimeNeedDetail = $thirdTimeNeedDetail;

        return $this;
    }

    public function getThirdTimeDetail(): ?string
    {
        return $this->thirdTimeDetail;
    }

    public function setThirdTimeDetail(?string $thirdTimeDetail): self
    {
        $this->thirdTimeDetail = $thirdTimeDetail;

        return $this;
    }

    public function isThirdTimeMediasApproved(): bool
    {
        $approved = false;

        /** @var Media $media */
        foreach ($this->thirdTimeMedias as $media) {
            if ($media->getState() == MediaWorflowStateConstants::STATE_ACCEPTED) {
                $approved = true;
            }
        }

        return $approved;
    }

    public function getThirdTimeMedias(): Collection
    {
        return $this->thirdTimeMedias;
    }

    public function addThirdTimeMedia(Media $thirdTimeMedia): self
    {
        if (!$this->thirdTimeMedias->contains($thirdTimeMedia)) {
            $this->thirdTimeMedias[] = $thirdTimeMedia;
        }

        return $this;
    }

    public function removeThirdTimeMedia(Media $thirdTimeMedia): self
    {
        $thirdTimeMedia->setState(MediaWorflowStateConstants::STATE_CANCELLED);
        $this->thirdTimeMedias->removeElement($thirdTimeMedia);

        return $this;
    }

    /**
     * Boursier ou non
     */
    public function getScholarShip(): ?bool
    {
        return $this->scholarShip;
    }

    public function setScholarShip(?bool $scholarShip): self
    {
        $this->scholarShip = $scholarShip;

        return $this;
    }

    /**
     * Documents de bourse
     */
    public function getScholarShipMedias(): Collection
    {
        return $this->scholarShipMedias;
    }

    public function addScholarShipMedia(Media $scholarShipMedia): self
    {
        if (!$this->scholarShipMedias->contains($scholarShipMedia)) {
            $this->scholarShipMedias[] = $scholarShipMedia;
        }

        return $this;
    }

    public function removeScholarShipMedia(Media $scholarShipMedia): self
    {
        $scholarShipMedia->setState(MediaWorflowStateConstants::STATE_CANCELLED);
        $this->scholarShipMedias->removeElement($scholarShipMedia);

        return $this;
    }

    public function getScholarShipLevel(): ?ScholarShipLevel
    {
        return $this->scholarShipLevel;
    }

    public function setScholarShipLevel(?ScholarShipLevel $scholarShipLevel): self
    {
        $this->scholarShipLevel = $scholarShipLevel;

        return $this;
    }

    public function getIdCards(): Collection
    {
        return $this->idCards;
    }

    public function addIdCard(Media $idCard): self
    {
        if (!$this->idCards->contains($idCard)) {
            $this->idCards[] = $idCard;
        }

        return $this;
    }

    public function removeIdCard(Media $idCard): self
    {
        $idCard->setState(MediaWorflowStateConstants::STATE_CANCELLED);
        $this->idCards->removeElement($idCard);

        return $this;
    }

    public function getStudentDiplomas(): Collection
    {
        return $this->studentDiplomas;
    }

    public function setStudentDiplomas(Collection $studentDiplomas): AdministrativeRecord
    {
        $this->studentDiplomas = $studentDiplomas;

        return $this;
    }

    public function addStudentDiploma(StudentDiploma $studentDiploma): self
    {
        if (!$this->studentDiplomas->contains($studentDiploma)) {
            $this->studentDiplomas[] = $studentDiploma;
        }

        $studentDiploma->setAdministrativeRecord($this);

        return $this;
    }

    public function removeStudentDiploma(StudentDiploma $studentDiploma): self
    {
        $this->studentDiplomas->removeElement($studentDiploma);

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        if (null !== $student && $student->getAdministrativeRecord() !== $this) {
            $student->setAdministrativeRecord($this);
        }

        return $this;
    }

    /**
     * Dernier diplome obtenue. Cette valeur est ecrit à la normalization.
     * Attention ! Il ne sera pas réécrit à la denormalization
     */
    public function getStudentLastDiploma(): ?StudentDiploma
    {
        return $this->studentLastDiploma;
    }

    public function setStudentLastDiploma(?StudentDiploma $studentLastDiploma): self
    {
        $this->studentLastDiploma = $studentLastDiploma;

        return $this;
    }

    /**
     * Attestation Journée Défense et Citoyenneté
     */
    public function getJdc(): ?Media
    {
        return $this->jdc;
    }

    public function setJdc(?Media $jdc): self
    {
        $this->jdc = $jdc;

        return $this;
    }

    public function getCv(): ?Cv
    {
        return $this->getStudent()->getCv();
    }
}
