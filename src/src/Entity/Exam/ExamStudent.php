<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\Traits\DateTrait;
use App\Repository\Exam\ExamStudentRepository;
use App\Validator as Validator;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ExamStudentRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user',
            'normalization_context' => ['groups' => ['examStudent:collection:read']],
        ],
        'post' => [
            'normalization_context' => ['groups' => ['examStudent:collection:read']],
            'denormalization_context' => ['groups' => ['examStudent:collection:write']],
            'validation_groups' => ['api'],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'security_post_denormalize' => 'is_granted("create", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user',
            'normalization_context' => ['groups' => ['examStudent:item:read']],
            'denormalization_context' => ['groups' => ['examStudent:item:write']],
        ],
        'put' => [
            'normalization_context' => ['groups' => ['examStudent:item:read']],
            'denormalization_context' => ['groups' => ['examStudent:item:write']],
            'validation_groups' => ['api'],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user and is_granted("edit", object)',
        ],
    ],
    subresourceOperations: [
        'api_students_exam_students_get_subresource' => [
            'method' => 'GET',
            'normalization_context' => [
                'groups' => ['exam_student:sub:read'],
            ],
        ],
    ],
)]

#[Assert\Sequentially([
    new Validator\ExamStudent\AvoidCollisionOnSameSession(groups: ['api', 'bo']),
    new Validator\ExamStudent\ExpectedWorkflowHistory(groups: ['api', 'bo']),
    new Validator\ExamStudent\AvoidCollisionOnOtherCampus(groups: ['api', 'bo']),
    new Validator\ExamStudent\AvoidCollisionOnSameSessionType(groups: ['api', 'bo']),
    new Validator\ExamStudent\CheckScoreNotExisting(groups: ['api', 'bo']),
])]
class ExamStudent
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'exam_student:sub:read',
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ExamSession::class, inversedBy: 'examStudents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Gedmo\Versioned]
    #[Assert\NotNull(groups: ['examStudent:write', 'examStudent:edit'])]
    #[Groups([
        'examStudent:item:read',
        'examStudent:item:write',
        'examStudent:collection:read',
        'examStudent:collection:write',
        'exam_student:sub:read',
    ])]
    private ?ExamSession $examSession = null;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'examStudents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([
        'session:collection:read',
        'examStudent:item:read',
        'examStudent:item:write',
        'examStudent:collection:read',
        'examStudent:collection:write',
    ])]
    #[Assert\NotNull(groups: ['api'])]
    #[Validator\ExpectedUser(
        expression: "object.getUser()",
        groups: ['api'],
    )]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: ExamRoom::class, inversedBy: 'examStudents')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'examStudent:item:read',
        'examStudent:item:write',
        'examStudent:collection:read',
        'examStudent:collection:write',
        'exam_student:sub:read',
    ])]
    private ?ExamRoom $examRoom = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Validator\RequiredField(
        expression: 'this.getMedia() !== null ',
        message: 'Vous devez renseigner un score',
        nullValues: [null, ''],
        groups: ['api'],
    )]
    #[Groups([
        'examStudent:item:read',
        'examStudent:item:write',
        'examStudent:collection:read',
        'examStudent:collection:write',
        'exam_student:sub:read',
    ])]
    private ?float $score = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\GreaterThanOrEqual(0, message: 'La note doit etre superieur a 0')]
    #[Assert\LessThanOrEqual(2000, message: 'La note doit etre inferieur a 20')]
    private ?float $admissibilityNote = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'session:collection:read',
        'examStudent:item:read',
        'examStudent:collection:read',
    ])]
    private ?bool $absent = null;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\Valid(groups: ['api'])]
    #[Validator\RequiredField(
        expression: 'this.getScore() !== null ',
        message: 'Vous devez ajouter un document',
        nullValues: [null, ''],
        groups: ['api'],
    )]
    #[Groups([
        'examStudent:item:read',
        'examStudent:item:write',
        'examStudent:collection:read',
        'examStudent:collection:write',
        'exam_student:sub:read',
    ])]
    private Media|null $media = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'session:collection:read',
        'examStudent:item:read',
        'examStudent:collection:read',
    ])]
    private bool $specific = false;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'session:collection:read',
        'examStudent:item:read',
        'examStudent:collection:read',
        'exam_student:sub:read',
    ])]
    private int $confirmed = 0;

    #[ORM\OneToOne(mappedBy: 'examStudent', cascade: ['persist', 'remove'])]
    #[Groups([
        'session:collection:read',
        'examStudent:item:read',
        'examStudent:collection:read',
        'exam_student:sub:read',
    ])]
    private ?ExamSummon $examSummon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getExamSession(): ?ExamSession
    {
        return $this->examSession;
    }

    public function setExamSession(?ExamSession $examSession): self
    {
        $this->examSession = $examSession;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getExamRoom(): ?ExamRoom
    {
        return $this->examRoom;
    }

    public function setExamRoom(?ExamRoom $examRoom): self
    {
        $this->examRoom = $examRoom;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getAdmissibilityNote(): ?float
    {
        return $this->admissibilityNote;
    }

    public function setAdmissibilityNote(?float $admissibilityNote): self
    {
        $this->admissibilityNote = $admissibilityNote;

        return $this;
    }

    public function getAbsent(): ?bool
    {
        return $this->absent;
    }

    public function setAbsent(?bool $absent): self
    {
        $this->absent = $absent;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Tier temps spécifique
     *
     * @return boolean
     */
    public function isSpecific(): bool
    {
        return $this->specific;
    }

    public function setSpecific(bool $specific): self
    {
        $this->specific = $specific;

        return $this;
    }

    public function isConfirmed(): int
    {
        return $this->confirmed;
    }

    public function setConfirmed(int $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getExamSummon(): ?ExamSummon
    {
        return $this->examSummon;
    }

    public function setExamSummon(ExamSummon $examSummon): self
    {
        // set the owning side of the relation if necessary
        if ($examSummon->getExamStudent() !== $this) {
            $examSummon->setExamStudent($this);
        }

        $this->examSummon = $examSummon;

        return $this;
    }
}