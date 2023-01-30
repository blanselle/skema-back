<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\ProgramChannel;
use App\Entity\Traits\DateTrait;
use App\Filter\ExamClassification\ActiveExamSessionFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['classification:collection:read']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['classification:collection:read']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
    ],
    order: ['name' => 'ASC'],
)]
#[ApiFilter(filterClass: ActiveExamSessionFilter::class, properties: [
    'active' => 'exact',
])]
#[ApiFilter(SearchFilter::class, properties: ['examSessionType.id' => 'exact', 'programChannels.id' => 'exact'])]
class ExamClassification
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'exam_student:sub:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'classification:collection:read',
        'session:collection:read',
        'exam_student:sub:read',
        'order:collection:read',
    ])]
    private string $name;

    #[ORM\ManyToOne(targetEntity: ExamSessionType::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'classification:collection:read',
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private ExamSessionType $examSessionType;

    #[ORM\ManyToOne(targetEntity: ExamCondition::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'classification:collection:read',
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private ExamCondition $examCondition;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class)]
    #[Groups([
        'classification:collection:read',
        'session:collection:read',
    ])]
    private Collection $programChannels;

    #[ORM\OneToMany(mappedBy: 'examClassification', targetEntity: ExamClassificationScore::class, cascade: ['remove'])]
    private Collection $examClassificationScores;

    #[ORM\OneToMany(mappedBy: 'examClassification', targetEntity: ExamSession::class)]
    #[Groups([
        'classification:collection:read',
    ])]
    private Collection $examSessions;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $equipment = null;

    #[ORM\Column(type: 'string', length: 180, nullable: true, options: ['default' => null])]
    private ?string $key = null;

    public function __construct()
    {
        $this->programChannels = new ArrayCollection();
        $this->examClassificationScores = new ArrayCollection();
        $this->examSessions = new ArrayCollection();
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

    public function getExamSessionType(): ExamSessionType
    {
        return $this->examSessionType;
    }

    public function setExamSessionType(ExamSessionType $examSessionType): self
    {
        $this->examSessionType = $examSessionType;

        return $this;
    }

    public function getExamCondition(): ExamCondition
    {
        return $this->examCondition;
    }

    public function setExamCondition(ExamCondition $examCondition): self
    {
        $this->examCondition = $examCondition;

        return $this;
    }

    public function getProgramChannels(): ArrayCollection|Collection
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

    public function getExamClassificationScores(): ArrayCollection|Collection
    {
        return $this->examClassificationScores;
    }

    public function setExamClassificationScores(ArrayCollection|Collection $examClassificationScores): ExamClassification
    {
        $this->examClassificationScores = $examClassificationScores;

        return $this;
    }

    public function addExamClassificationScore(ExamClassificationScore $examClassificationScore): ExamClassification
    {
        if (!$this->examClassificationScores->contains($examClassificationScore)) {
            $this->examClassificationScores->add($examClassificationScore);
        }

        return $this;
    }

    public function removeExamClassificationScore(ExamClassificationScore $examClassificationScore): ExamClassification
    {
        if ($this->examClassificationScores->contains($examClassificationScore)) {
            $this->examClassificationScores->removeElement($examClassificationScore);
        }

        return $this;
    }

    public function getExamSessions(): ArrayCollection|Collection
    {
        return $this->examSessions;
    }

    public function setExamSessions(ArrayCollection|Collection $examSessions): ExamClassification
    {
        $this->examSessions = $examSessions;

        return $this;
    }

    public function addExamSession(ExamSession $examSession): ExamClassification
    {
        if (!$this->examSessions->contains($examSession)) {
            $this->examSessions->add($examSession);
        }

        return $this;
    }

    public function removeExamSession(ExamSession $examSession): ExamClassification
    {
        if ($this->examSessions->contains($examSession)) {
            $this->examSessions->removeElement($examSession);
        }

        return $this;
    }

    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    public function setEquipment(?string $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }
}
