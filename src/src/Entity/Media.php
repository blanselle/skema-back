<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\Media\CreateMedia;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaTypeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\CV\Cv;
use App\Entity\Loggable\History;
use App\Entity\Traits\DateTrait;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Repository\MediaRepository;
use App\Validator\Media\MediaFormFileNotNull;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['media:collection:read'],
            ]
        ],
        'post' => [
            'controller' => CreateMedia::class,
            'normalization_context' => ['groups' => ['media:collection:read']],
            'denormalization_context' => ['groups' => ['media:collection:write']],
            'input_formats' => [
                'multipart' => ['multipart/form-data'],
            ],
            'deserialize' => false,
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['media:item:read'],
            ]
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ]
    ],
)]
class Media implements CvCalculationInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'media:item:read',
        'media:collection:read'
    ])]
    private ?int $id = null;

    #[Assert\File(
        mimeTypes: ['image/jpeg', 'image/png', 'image/svg+xml', 'image/gif', 'application/pdf', 'application/x-pdf'],
        mimeTypesMessage: 'L\'image doit être dans l\'un des formats suivants : {{ types }}'
    )]
    #[MediaFormFileNotNull(groups: ['bo'])]
    #[Groups([
        'media:collection:write'
    ])]
    private ?File $formFile = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
        'ar:item:read',
        'sd:item:read',
        'sd:collection:read',
        'media:item:read',
        'media:collection:read',
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:collection:read',
        'schoolReport:item:read',
        'schoolReport:collection:read',
        'examStudent:item:read',
        'exam_student:sub:read',
    ])]
    private ?string $file = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups([
        'media:collection:write'
    ])]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Choice(
        callback: [MediaTypeConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
    )]
    private string $type = MediaTypeConstants::TYPE_IMAGE_CMS;

    #[ORM\Column]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
        'ar:item:read',
        'sd:item:read',
        'sd:collection:read',
        'media:item:read',
        'media:collection:read',
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:collection:read',
        'schoolReport:item:read',
        'schoolReport:collection:read',
        'examStudent:item:read',
        'exam_student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private string $state = MediaWorflowStateConstants::STATE_UPLOADED;

    #[ORM\Column(nullable: true)]
    private ?string $transition = '';

    #[ORM\Column(type: 'string', length: 100)]
    #[Groups([
        'media:collection:write',
        'campus:item:read',
        'campus:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
        'ar:item:read',
        'sd:item:read',
        'sd:collection:read',
        'media:item:read',
        'media:collection:read',
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:collection:read',
        'schoolReport:item:read',
        'schoolReport:collection:read',
        'examStudent:item:read',
        'exam_student:sub:read',
    ])]
    #[Assert\NotBlank()]
    #[Assert\NotNull()]
    #[Assert\Choice(
        callback: [MediaCodeConstants::class, 'getConsts'],
        message: "Code {{ value }} invalide, veuillez renseigner l'un des codes suivants : {{ choices }}"
    )]
    private string $code = MediaCodeConstants::CODE_AUTRE;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Student $student = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'campus:item:read',
        'campus:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
        'ar:item:read',
        'sd:item:read',
        'sd:collection:read',
        'media:item:read',
        'media:collection:read',
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:collection:read',
        'schoolReport:item:read',
        'schoolReport:collection:read',
        'examStudent:item:read',
        'exam_student:sub:read',
    ])]
    #[Gedmo\Versioned]
    private ?string $originalName = null;

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormFile(): ?File
    {
        return $this->formFile;
    }

    public function setFormFile(?File $formFile): self
    {
        $this->formFile = $formFile;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

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

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getTransition(): ?string
    {
        return $this->transition;
    }

    public function setTransition(?string $transition): self
    {
        $this->transition = $transition;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): Media
    {
        $this->student = $student;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getCv(): ?Cv
    {
        return $this->getStudent()?->getCv();
    }
}
