<?php

declare(strict_types=1);

namespace App\Entity\CV;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Repository\CV\SchoolReportRepository;
use App\Validator\Cv\SchoolReportScoreMissing;
use App\Validator\Cv\SchoolReportMediaMissing;
use App\Validator\ExpectedUser;
use App\Validator\SchoolReportCount;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SchoolReportRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => ['groups' => ['schoolReport:collection:read']],
            'denormalization_context' => ['groups' => ['schoolReport:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'security_post_denormalize' => 'is_granted("create", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['schoolReport:item:read']],
            'denormalization_context' => ['groups' => ['schoolReport:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getBacSup().getCv().getStudent().getUser()'
        ],
        'put' => [
            'normalization_context' => ['groups' => ['schoolReport:item:read']],
            'denormalization_context' => ['groups' => ['schoolReport:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getBacSup().getCv().getStudent().getUser()',
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
    ],
)]

/**
 * Bulletin d'un CV
 */
class SchoolReport implements CvCalculationInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\Expression(
        "this.isScoreNotOutOfTwenty() !== true || (0 <= this.getScore() && this.getScore() <= 20.0)",
        message:"Le score doit être compris entre 0 et 20.",
        groups: ['api', 'bo'],
    )]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:item:read',
        'schoolReport:item:read',
        'schoolReport:item:write',
        'schoolReport:collection:read',
        'schoolReport:collection:write',
    ])]
    #[SchoolReportScoreMissing(groups: ['bo', 'cv:validation', 'api'])]
    #[Gedmo\Versioned]
    private ?float $score = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Assert\GreaterThanOrEqual(0, message: 'La note doit etre superieur a 0', groups: ['api', 'bo'])]
    #[Assert\LessThanOrEqual(20, message: 'La note doit etre inferieur a 20', groups: ['api', 'bo'])]
    #[Gedmo\Versioned]
    private ?float $scoreRetained = null;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:item:read',
        'schoolReport:item:read',
        'schoolReport:item:write',
        'schoolReport:collection:read',
        'schoolReport:collection:write',
    ])]
    #[SchoolReportMediaMissing(groups: ['bo', 'cv:validation', 'api'])]
    private ?Media $media = null;

    #[ORM\ManyToOne(targetEntity: BacSup::class, inversedBy: 'schoolReports')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Groups([
        'schoolReport:item:read',
        'schoolReport:item:write',
        'schoolReport:collection:read',
        'schoolReport:collection:write',
    ])]
    #[ExpectedUser(
        expression: "object.getCv().getStudent().getUser()",
        groups: ['api']
    )]
    #[SchoolReportCount(groups: ['api', 'bo'])]
    #[Gedmo\Versioned]
    private BacSup $bacSup;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:item:read',
        'schoolReport:item:read',
        'schoolReport:item:write',
        'schoolReport:collection:read',
        'schoolReport:collection:write',
    ])]
    #[Gedmo\Versioned]
    private bool $additionnal = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bacSup:collection:read',
        'bacSup:item:read',
        'schoolReport:item:read',
        'schoolReport:item:write',
        'schoolReport:collection:read',
        'schoolReport:collection:write',
    ])]
    #[Assert\Expression(
        "this.isScoreNotOutOfTwenty() !== true || null === this.getScore()",
        message:"Le score ne doit pas être renseigné si la note retenue n\'est pas sur 20.",
        groups: ['api', 'bo'],
    )]
    #[Gedmo\Versioned]
    private ?bool $scoreNotOutOfTwenty = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getScoreRetained(): ?float
    {
        return $this->scoreRetained;
    }

    public function setScoreRetained(?float $scoreRetained): self
    {
        $this->scoreRetained = $scoreRetained;

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

    public function getBacSup(): BacSup
    {
        return $this->bacSup;
    }

    public function setBacSup(BacSup $bacSup): self
    {
        $bacSup->addSchoolReport($this);

        $this->bacSup = $bacSup;

        return $this;
    }

    /**
     * Le diplome est un double parcours
     */
    public function getAdditionnal(): bool
    {
        return $this->additionnal;
    }

    public function setAdditionnal(bool $additionnal): self
    {
        $this->additionnal = $additionnal;

        return $this;
    }

    public function isScoreNotOutOfTwenty(): ?bool
    {
        return $this->scoreNotOutOfTwenty;
    }

    public function setScoreNotOutOfTwenty(?bool $scoreNotOutOfTwenty): self
    {
        $this->scoreNotOutOfTwenty = $scoreNotOutOfTwenty;

        return $this;
    }

    public function getCv(): Cv
    {
        return $this->getBacSup()->getCv();
    }
}
