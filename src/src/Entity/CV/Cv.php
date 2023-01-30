<?php

declare(strict_types=1);

namespace App\Entity\CV;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\Cv\Validation;
use App\Entity\CV\Bac\Bac;
use App\Entity\Loggable\History;
use App\Entity\Student;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Repository\CV\CvRepository;
use App\Validator\Cv\BacSupLevel;
use App\Validator\Cv\NoExperience;
use App\Validator\ExpectedUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CvRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => ['groups' => ['cv:collection:read']],
            'denormalization_context' => ['groups' => ['cv:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'validation_groups' => ['api'],
            'security_post_denormalize' => 'is_granted("create", object)',
        ],
        'validation' => [
            'method' => 'POST',
            'path' => '/student_cvs/{id}/validate',
            'controller' => Validation::class,
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['cv:item:read']],
            'denormalization_context' => ['groups' => ['cv:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getStudent().getUser()',
            'validation_groups' => ['api'],
        ],
        'put' => [
            'normalization_context' => ['groups' => ['cv:item:read']],
            'denormalization_context' => ['groups' => ['cv:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getStudent().getUser()',
            'validation_groups' => ['api'],
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
    ],
    shortName: 'student_cv',
)]
/**
 * Info sur le CV d'un etudiant
 */
class Cv implements CvCalculationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'cv', targetEntity: Bac::class, cascade: ['persist', 'remove'])]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Expression(
        'this.getValidated() === false',
        message: 'Le Cv est déjà validé',
        groups: ['api']
    )]
    #[Assert\NotNull(groups: ['cv:validation'], message: 'Veuillez enregistrer un bac')]
    #[Assert\Valid(groups: ['bo', 'cv:validation'])]
    private ?Bac $bac = null;

    #[ORM\OneToMany(mappedBy: 'cv', targetEntity: Experience::class, cascade: ['remove'])]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Valid(groups: ['bo', 'cv:validation'])]
    #[NoExperience(groups: ['cv:validation'])]
    private Collection $experiences;

    #[ORM\OneToMany(mappedBy: 'cv', targetEntity: BacSup::class, cascade: ['remove'])]
    #[ORM\OrderBy(['year' => 'ASC'])]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Expression(
        'this.getValidated() === false',
        message: 'Le Cv est déjà validé',
        groups: ['api']
    )]
    #[Assert\Valid(groups: ['bo', 'cv:validation'])]
    #[BacSupLevel(groups: ['cv:validation'])]
    private Collection $bacSups;

    #[ORM\OneToOne(inversedBy: 'cv', targetEntity: Student::class)]
    #[Assert\NotNull(message: 'Impossible d\'avoir un cv sans etudiant')]
    #[ExpectedUser(
        expression: "object.getUser()",
        groups: ['api'],
    )]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    private Student $student;

    #[ORM\ManyToMany(targetEntity: Language::class)]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Expression(
        'this.getValidated() === false',
        message: 'Le Cv est déjà validé',
        groups: ['api']
    )]
    private Collection $languages;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Expression(
        'this.getValidated() === false',
        message: 'Le Cv est déjà validé',
        groups: ['api']
    )]
    #[Assert\NotNull(groups: ['cv:validation'])]
    #[Gedmo\Versioned]
    private ?bool $noProfessionalExperience = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Expression(
        'this.getValidated() === false',
        message: 'Le Cv est déjà validé',
        groups: ['api']
    )]
    #[Assert\NotNull(groups: ['cv:validation'])]
    #[Gedmo\Versioned]
    private ?bool $noAssociativeExperience = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:item:write',
        'cv:collection:write',
        'cv:collection:read',
    ])]
    #[Assert\Expression(
        'this.getValidated() === false',
        message: 'Le Cv est déjà validé',
        groups: ['api']
    )]
    #[Assert\NotNull(groups: ['cv:validation'])]
    #[Gedmo\Versioned]
    private ?bool $noInternationnalExperience = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
    ])]
    private bool $validated = false;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $note = null;


    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $bonus = null;

    public function __construct()
    {
        $this->experiences = new ArrayCollection();
        $this->bacSups = new ArrayCollection();
        $this->languages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getBac(): ?Bac
    {
        return $this->bac;
    }

    public function setBac(?Bac $bac): self
    {
        $this->bac = $bac;
        if(null !== $bac && $bac->getCv() !== $this) {
            $bac->setCv($this);
        }
        

        return $this;
    }

    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setCv($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        $this->experiences->removeElement($experience);

        return $this;
    }

    public function getBacSups(): Collection
    {
        return $this->bacSups;
    }

    public function setBacSups(Collection $bacSups): Cv
    {
        $this->bacSups = $bacSups;

        return $this;
    }

    public function addBacSup(BacSup $bacSup): self
    {
        if (!$this->bacSups->contains($bacSup)) {
            $this->bacSups[] = $bacSup;
            $bacSup->setCv($this);
        }

        return $this;
    }

    public function removeBacSup(BacSup $bacSup): self
    {
        $this->bacSups->removeElement($bacSup);

        return $this;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): self
    {
        $this->student = $student;

        if ($student->getCv() !== $this) {
            $student->setCv($this);
        }

        return $this;
    }

    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->languages->contains($language)) {
            $this->languages[] = $language;
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        $this->languages->removeElement($language);

        return $this;
    }

    public function getNoProfessionalExperience(): ?bool
    {
        return $this->noProfessionalExperience;
    }

    public function setNoProfessionalExperience(?bool $noProfessionalExperience): self
    {
        $this->noProfessionalExperience = $noProfessionalExperience;

        return $this;
    }

    public function getNoAssociativeExperience(): ?bool
    {
        return $this->noAssociativeExperience;
    }

    public function setNoAssociativeExperience(?bool $noAssociativeExperience): self
    {
        $this->noAssociativeExperience = $noAssociativeExperience;

        return $this;
    }

    public function getNoInternationnalExperience(): ?bool
    {
        return $this->noInternationnalExperience;
    }

    public function setNoInternationnalExperience(?bool $noInternationnalExperience): self
    {
        $this->noInternationnalExperience = $noInternationnalExperience;

        return $this;
    }

    public function getValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getBonus(): ?float
    {
        return $this->bonus;
    }

    public function setBonus(?float $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getCv(): ?Cv
    {
        return $this;
    }
}
