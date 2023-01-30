<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Action\Student\AccountActivationController;
use App\Action\Student\Resignation;
use App\Action\Student\SubmitCandidacy;
use App\Action\Student\Summons;
use App\Constants\User\SimplifiedStudentStatusConstants;
use App\Constants\User\StudentConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Dto\AdmissibilityPublicationOutput;
use App\Dto\AdmissibilityResultOutput;
use App\Dto\CandidacyOutput;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\CV\Cv;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Entity\Exam\ExamSummon;
use App\Entity\Loggable\History;
use App\Entity\OralTest\OralTestStudent;
use App\Entity\Parameter\ParameterKey;
use App\Entity\Payment\Order;
use App\Entity\User\StudentWorkflowHistory;
use App\Repository\StudentRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Action\Student\LandingAdmissibilityPublicationController;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ORM\HasLifecycleCallbacks()]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['student:collection:read'],
            ],
            'security' => 'is_granted("ROLE_COORDINATOR")',
        ],
        'summons' => [
            'method' => 'GET',
            'path' => '/students/summons',
            'controller' => Summons::class,
            'normalization_context' => [
                'groups' => ['user:item:read-me'],
            ],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
        'activation' => [
            'method' => 'POST',
            'path' => '/students/activation',
            'controller' => AccountActivationController::class,
            'security' => "is_granted('PUBLIC_ACCESS')",
        ],
        'landing_admissibility_publication' => [
            'method' => 'GET',
            'path' => '/students/landing_admissibility_publication',
            'status' => 200,
            'controller' => LandingAdmissibilityPublicationController::class,
            'security' => "is_granted('PUBLIC_ACCESS')",
            'requirements' => ['token' => '.+'],
            'openapi_context' => [
                'summary' => 'Landing Admissibility Publication',
                'description' => 'Landing Admissibility Publication Page',
                'parameters' => [
                    [
                        'name' => 'token',
                        'description' => 'The token to get admissibility result',
                        'type' => 'string',
                        'in' => 'query',
                        'required' => true
                    ],
                ]
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['student:item:read'],
            ],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getUser() == user'
        ],
        'resignation' => [
            'method' => 'PUT',
            'path' => '/students/{id}/resignation',
            'controller' => Resignation::class,
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getUser() == user',
        ],
        'candidacy_steps' => [
            'method' => 'GET',
            'path' => '/students/{id}/candidacy_steps',
            'status' => 200,
            'output' => CandidacyOutput::class,
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getUser() == user',
            'openapi_context' => [
                'summary' => 'Candidacy',
                'description' => 'Completion candidacy status',
            ],
        ],
        'submit_candidacy' => [
            'method' => 'POST',
            'path' => '/students/{id}/submit_candidacy',
            'status' => 200,
            'controller' => SubmitCandidacy::class,
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getUser() == user',
            'openapi_context' => [
                'summary' => 'Submit candidacy',
                'description' => 'Submit the candidacy',
            ]
        ],
        'get_admissibility_result' => [
            'method' => 'GET',
            'path' => '/students/{id}/admissibility_result',
            'status' => 200,
            'output' => AdmissibilityResultOutput::class,
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getUser() == user and is_granted("show_result", object)',
            'openapi_context' => [
                'summary' => 'Admissibility Result',
                'description' => 'Admissibility result and score of student',
            ],
        ],
        'get_admissibility_publication' => [
            'method' => 'GET',
            'path' => '/students/{id}/admissibility_publication',
            'status' => 200,
            'output' => AdmissibilityPublicationOutput::class,
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getUser() == user and is_granted("show_result", object)',
            'openapi_context' => [
                'summary' => 'Admissibility publication result',
                'description' => 'The content to display the publication',
            ],
        ],
    ],
    subresourceOperations: [
        'api_users_student_get_subresource' => [
            'method' => 'GET',
            'normalization_context' => [
                'groups' => ['student:sub:read'],
            ],
        ],
    ],
)]
/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Student
{
    use Traits\DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[ApiProperty(writable: false)]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:item:read-me',
        'student:sub:read',
        'user:item:write'
    ])]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(groups: ['user:validation'])]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private DateTime $dateOfBirth;

    #[ORM\ManyToOne(targetEntity: ProgramChannel::class, inversedBy: 'students')]
    #[Assert\NotNull(groups: ['user:validation'])]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'student:sub:read',
        'user:item:read-me',
    ])]
    #[Gedmo\Versioned]
    private ProgramChannel $programChannel;

    #[ORM\OneToOne(mappedBy: 'student', targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups([
        'user:item:read-me',
    ])]
    #[Gedmo\Versioned]
    private string $state = StudentWorkflowStateConstants::STATE_START;

    #[ORM\Column(nullable: true)]
    private ?string $transition = '';

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: StudentWorkflowHistory::class)]
    private Collection $workflowHistories;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private ?string $firstNameSecondary;

    #[ORM\Column(type: 'string', length: 1, nullable: true)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Assert\Choice(
        callback: [StudentConstants::class, 'getConsts'],
        message: 'Genre incorrect, il doit être égal à l\'une des valeurs suivantes : {{ choices }}',
        groups: ['user:validation'],
    )]
    #[Assert\NotNull]
    #[Gedmo\Versioned]
    private ?string $gender;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:item:read-me',
        'student:sub:read',
    ])]
    private ?string $identifier = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Assert\NotNull(
        message: "Le telephone ne peut pas être nul",
        groups: ['user:validation']
    )]
    #[Assert\NotBlank(
        message: "Le telephone ne peut pas être vide",
        groups: ['user:validation']
    )]
    #[Assert\Regex(
        "/^(\+?)\d{5,20}$/",
        "Le telephone n'est pas au bon format",
        groups: ['user:validation']
    )]
    #[Gedmo\Versioned]
    private string $phone;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private string $address;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private string $postalCode;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private string $city;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private Country $country;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private Country $countryBirth;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private Country $nationality;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private ?Country $nationalitySecondary = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: ExamStudent::class, orphanRemoval: true)]
    #[Groups([
        'user:item:read-me',
    ])]
    #[ApiSubresource]
    private Collection $examStudents;

    #[ORM\OneToOne(mappedBy: 'student', targetEntity: AdministrativeRecord::class, cascade: ['persist', 'remove'])]
    #[Assert\NotNull(groups: ['user:validation'])]
    #[Assert\Valid(groups: ['user:validation'])]
    #[ApiSubresource]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:item:read-me',
        'student:sub:read',
        'user:collection:write',
        'user:item:write',
    ])]
    private ?AdministrativeRecord $administrativeRecord = null;

    #[ORM\OneToOne(mappedBy: 'student', targetEntity: Cv::class, cascade: ['persist'], fetch: 'EXTRA_LAZY')]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:item:read-me',
    ])]
    private ?Cv $cv = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
        'student:sub:read',
    ])]
    private bool $competitionFeesPayment = false;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $admissibilityGlobalNote = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $admissibilityRanking = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $admissibilityGlobalScore = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $admissibilityMaxScore = null;

    #[ORM\OneToOne(targetEntity: ExamStudent::class, cascade: ['persist', 'remove'])]
    private ?ExamStudent $englishNoteUsed = null;

    #[ORM\OneToOne(targetEntity: ExamStudent::class, cascade: ['persist', 'remove'])]
    private ?ExamStudent $managementNoteUsed = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: ExamSummon::class)]
    private Collection $examSummons;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: History::class, fetch: "EXTRA_LAZY", orphanRemoval: true)]
    private Collection $histories;

    #[Groups(['user:item:read-me'])]
    /** @phpstan-ignore-next-line */
    private string $simplifiedStatus;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Order::class, fetch: "EXTRA_LAZY", orphanRemoval: true)]
    private Collection $orders;

    #[Gedmo\Versioned]
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $anonymized = false;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: OralTestStudent::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $oralTestStudents;

    public function __construct()
    {
        $this->examStudents = new ArrayCollection();
        $this->examSummons = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->oralTestStudents = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s - %s - %s - %s',
            $this->identifier,
            $this->user->getFirstName(),
            $this->user->getLastName(),
            $this->programChannel->getName()
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Student
    {
        $this->id = $id;

        return $this;
    }

    public function getDateOfBirth(): ?DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(DateTime $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getProgramChannel(): ?ProgramChannel
    {
        return $this->programChannel;
    }

    public function setProgramChannel(?ProgramChannel $programChannel): self
    {
        $this->programChannel = $programChannel;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        if ($this !== $user->getStudent()) {
            $user->setStudent($this);
        }
        $this->user = $user;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): Student
    {
        $this->state = $state;

        return $this;
    }

    public function getTransition(): ?string
    {
        return $this->transition;
    }

    public function setTransition(?string $transition): Student
    {
        $this->transition = $transition;

        return $this;
    }

    public function getWorkflowHistories(): Collection
    {
        return $this->workflowHistories;
    }

    public function setWorkflowHistories(Collection $workflowHistories): Student
    {
        $this->workflowHistories = $workflowHistories;

        return $this;
    }

    public function addWorkflowHistory(StudentWorkflowHistory $studentWorkflowHistory): self
    {
        if (!$this->workflowHistories->contains($studentWorkflowHistory)) {
            $this->workflowHistories[] = $studentWorkflowHistory;
            $studentWorkflowHistory->setStudent($this);
        }

        return $this;
    }

    public function removeWorkflowHistory(StudentWorkflowHistory $studentWorkflowHistory): self
    {
        $this->workflowHistories->removeElement($studentWorkflowHistory);

        return $this;
    }

    public function getFirstNameSecondary(): ?string
    {
        return $this->firstNameSecondary;
    }

    public function setFirstNameSecondary(?string $firstNameSecondary): self
    {
        $this->firstNameSecondary = $firstNameSecondary;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }


    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

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

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function setCountry(Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    #[ORM\PreFlush]
    public function initIdentifier(PreFlushEventArgs $event): void
    {
        if (null == $this->getIdentifier()) {
            $em = $event->getEntityManager();
            $parameterKey = $em->getRepository(ParameterKey::class)->findOneBy(['name' => 'anneeConcours']);
            $anneeConcours = $parameterKey?->getParameters()[0]?->getValue();

            if (null !== $anneeConcours) {
                $this->setIdentifier(sprintf("%d%'.04d", substr((string)$anneeConcours, -2), $this->getId()));
            }
        }
    }

    public function getCountryBirth(): Country
    {
        return $this->countryBirth;
    }

    public function setCountryBirth(Country $countryBirth): self
    {
        $this->countryBirth = $countryBirth;

        return $this;
    }

    public function getNationality(): Country
    {
        return $this->nationality;
    }

    public function setNationality(Country $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getNationalitySecondary(): ?Country
    {
        return $this->nationalitySecondary;
    }

    public function setNationalitySecondary(?Country $nationalitySecondary): self
    {
        $this->nationalitySecondary = $nationalitySecondary;

        return $this;
    }

    /**
     * @return Collection<int, ExamStudent>
     */
    public function getExamStudents(): Collection
    {
        return $this->examStudents;
    }

    public function addExamStudent(ExamStudent $examStudent): self
    {
        if (!$this->examStudents->contains($examStudent)) {
            $this->examStudents[] = $examStudent;
            $examStudent->setStudent($this);
        }

        return $this;
    }

    public function removeExamStudent(ExamStudent $examStudent): self
    {
        $this->examStudents->removeElement($examStudent);

        return $this;
    }

    public function getAdministrativeRecord(): ?AdministrativeRecord
    {
        return $this->administrativeRecord;
    }

    public function setAdministrativeRecord(?AdministrativeRecord $administrativeRecord): self
    {
        $this->administrativeRecord = $administrativeRecord;

        if ($administrativeRecord !== null) {
            $administrativeRecord->setStudent($this);
        }

        return $this;
    }

    /**
     * Le candidat a payer ses frais d'inscription
     */
    public function getCompetitionFeesPayment(): bool
    {
        return $this->competitionFeesPayment;
    }

    public function setCompetitionFeesPayment(bool $competitionFeesPayment): self
    {
        $this->competitionFeesPayment = $competitionFeesPayment;

        return $this;
    }

    public function getCv(): ?Cv
    {
        return $this->cv;
    }

    public function setCv(?Cv $cv): self
    {
        $this->cv = $cv;

        if (null !== $cv) {
            $cv->setStudent($this);
        }

        return $this;
    }

    public function getGlobalCvNote(): float
    {
        return $this->getCv()->getNote() + $this->getCv()->getBonus();
    }

    public function getAdmissibilityGlobalNote(): ?float
    {
        return $this->admissibilityGlobalNote;
    }

    public function setAdmissibilityGlobalNote(?float $admissibilityGlobalNote): self
    {
        if (null !== $admissibilityGlobalNote) {
            $this->admissibilityGlobalNote = round($admissibilityGlobalNote, 2);
        }

        return $this;
    }

    public function getAdmissibilityRanking(): ?int
    {
        return $this->admissibilityRanking;
    }

    public function setAdmissibilityRanking(?int $admissibilityRanking): self
    {
        $this->admissibilityRanking = $admissibilityRanking;

        return $this;
    }

    public function getAdmissibilityGlobalScore(): ?float
    {
        return $this->admissibilityGlobalScore;
    }

    public function setAdmissibilityGlobalScore(?float $admissibilityGlobalScore): self
    {
        $this->admissibilityGlobalScore = $admissibilityGlobalScore;

        return $this;
    }

    public function getAdmissibilityMaxScore(): ?float
    {
        return $this->admissibilityMaxScore;
    }

    public function setAdmissibilityMaxScore(?float $admissibilityMaxScore): self
    {
        $this->admissibilityMaxScore = $admissibilityMaxScore;

        return $this;
    }

    public function getEnglishNoteUsed(): ?ExamStudent
    {
        return $this->englishNoteUsed;
    }

    public function setEnglishNoteUsed(?ExamStudent $englishNoteUsed): self
    {
        $this->englishNoteUsed = $englishNoteUsed;

        return $this;
    }

    public function getManagementNoteUsed(): ?ExamStudent
    {
        return $this->managementNoteUsed;
    }

    public function setManagementNoteUsed(?ExamStudent $managementNoteUsed): self
    {
        $this->managementNoteUsed = $managementNoteUsed;

        return $this;
    }

    /**
     * @return Collection<int, ExamSummon>
     */
    public function getExamSummons(): Collection
    {
        return $this->examSummons;
    }

    /**
     * @return Collection<int, History>
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->setStudent($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            // set the owning side to null (unless already changed)
            if ($history->getStudent() === $this) {
                $history->setStudent(null);
            }
        }

        return $this;
    }

    public function getSimplifiedStatus(): ?string
    {
        return strtolower(SimplifiedStudentStatusConstants::getFromStatus($this->getState()));
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setStudent($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getStudent() === $this) {
                $order->setStudent(null);
            }
        }

        return $this;
    }

    public function getOrder(string $type, ?ExamSession $session = null): ?Order
    {
        $order = $this->getOrders()
            ->filter(function (Order $o) use ($type, $session) {
                if (null === $session) {
                    return $o->getType() === $type;
                }

                return $o->getType() === $type and $o->getExamSession() === $session;
            })
            ->first()
            ;

        return (false !== $order)? $order : null;
    }

    public function isAnonymized(): bool
    {
        return $this->anonymized;
    }

    public function setAnonymized(bool $anonymized): Student
    {
        $this->anonymized = $anonymized;

        return $this;
    }

    public function getOralTestStudents(): Collection
    {
        return $this->oralTestStudents;
    }

    public function setOralTestStudents(Collection $oralTestStudents): Student
    {
        $this->oralTestStudents = $oralTestStudents;

        return $this;
    }

    public function addOralTestStudent(OralTestStudent $oralTestStudent): Student
    {
        if (!$this->oralTestStudents->contains($oralTestStudent)) {
            $this->oralTestStudents[] = $oralTestStudent;
            $oralTestStudent->setStudent($this);
        }

        return $this;
    }

    public function removeOralTestStudent(OralTestStudent $oralTestStudent): Student
    {
        $this->oralTestStudents->removeElement($oralTestStudent);

        return $this;
    }
}
