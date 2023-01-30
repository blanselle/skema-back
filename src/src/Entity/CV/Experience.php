<?php

declare(strict_types=1);

namespace App\Entity\CV;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\Experience\GetDuration;
use App\Constants\CV\Experience\ExperienceStateConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Constants\CV\Experience\TimeTypeConstants;
use App\Entity\Loggable\History;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Repository\CV\ExperienceRepository;
use App\Validator as Validator;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => ['groups' => ['experience:collection:read']],
            'denormalization_context' => ['groups' => ['experience:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'validation_groups' => ['experience:validation'],
            'security_post_denormalize' => 'is_granted("create", object)',
        ],
        'experience_duration' => [
            'method' => 'POST',
            'path' => '/experiences/duration',
            'controller' => GetDuration::class,
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['experience:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getCv().getStudent().getUser()'
        ],
        'put' => [
            'normalization_context' => ['groups' => ['experience:item:read']],
            'denormalization_context' => ['groups' => ['experience:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getCv().getStudent().getUser()',
            'validation_groups' => ['experience:validation'],
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getCv().getStudent().getUser() and is_granted("delete", object)',
            'security_post_denormalize' => 'is_granted("delete", {\'original\': previous_object, \'object\': object})',
        ],
    ],
)]
#[Assert\Expression(
    'this.getCv().getValidated() === false',
    message: 'Le Cv est déjà validé',
    groups: ['experience:validation']
)]
/**
 * Experience professional d'un CV d'un etudiant
 */
class Experience implements CvCalculationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\Length(
        min: 1,
        max: 180,
        minMessage: 'Le nom de l\'établissement doit être plus grand que {{ limit }}',
        maxMessage: 'Le nom de l\'établissement doit être plus petit que {{ limit }}',
        groups: ['bo', 'experience:validation', 'cv:validation'],
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $establishment = '';

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(
        callback: [TimeTypeConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
        groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation'],
    )]
    #[Assert\NotNull(groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $timeType = TimeTypeConstants::FULL_TIME;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation'])]
    #[Validator\Experience\ExperienceDate(
        groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation']
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private DateTime $beginAt;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Assert\NotNull(groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation'])]
    #[Assert\GreaterThan(
        propertyPath: 'beginAt',
        message: 'La date de fin doit être supérieur à la date de début',
        groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation'],
    )]
    #[Validator\Parameter\LessOrEqualThanParameter(
        parameterName: 'dateClotureInscriptions',
        programChannelId: 'this.getCv().getStudent().getProgramChannel().getId()',
        message: 'La date de fin ne peut pas être ultérieure à {{ parameter }}',
        groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation']
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private ?DateTime $endAt;

    #[ORM\Column(type: 'text')]
    #[Assert\Length(
        min: 3,
        max: 500,
        minMessage: 'La description doit contenir plus de {{ limit }} caractère(s)',
        maxMessage: 'La description doit contenir moins de {{ limit }} caractère(s)',
        groups: ['bo', 'experience:validation', 'cv:validation'],
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $description;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Choice(
        callback: [ExperienceTypeConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
        groups: ['bo', 'experience:validation', 'experience:duration', 'cv:validation'],
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private string $experienceType = ExperienceTypeConstants::TYPE_PROFESSIONAL;

    #[ORM\ManyToOne(targetEntity: Cv::class, inversedBy: 'experiences')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Validator\ExpectedUser(
        expression: "object.getStudent().getUser()",
        groups: ['experience:validation', 'experience:duration', 'cv:validation'],
    )]
    private Cv $cv;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[AppAssert\Experience\HoursPerWeekMandatoryForPartialTime(groups: [
        'bo',
        'experience:validation',
        'experience:duration',
        'cv:validation',
    ])]
    #[Gedmo\Versioned]
    private ?int $hoursPerWeek = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'experience:item:read',
        'experience:item:write',
        'experience:collection:read',
        'experience:collection:write',
    ])]
    #[Gedmo\Versioned]
    private ?int $duration = null;

    #[ORM\Column(type: 'string', length: 20)]
    #[Gedmo\Versioned]
    private string $state = ExperienceStateConstants::STATE_ACCEPTED;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getTimeType(): string
    {
        return $this->timeType;
    }

    public function setTimeType(?string $timeType = null): self
    {
        $this->timeType = $timeType;

        return $this;
    }

    public function getBeginAt(): \DateTime
    {
        return $this->beginAt;
    }

    public function setBeginAt(\DateTime $beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTime
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTime $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getExperienceType(): string
    {
        return $this->experienceType;
    }

    public function setExperienceType(string $experienceType): self
    {
        $this->experienceType = $experienceType;

        return $this;
    }

    public function getCv(): Cv
    {
        return $this->cv;
    }

    public function setCv(Cv $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

    public function getHoursPerWeek(): ?int
    {
        return $this->hoursPerWeek;
    }

    public function setHoursPerWeek(?int $hoursPerWeek): self
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): Experience
    {
        $this->duration = $duration;

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }
}
