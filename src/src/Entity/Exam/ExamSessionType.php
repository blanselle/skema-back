<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['session_type:collection:read']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
    ]
)]
class ExamSessionType
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'session_type:collection:read',
        'classification:collection:read',
        'exam_student:sub:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'session_type:collection:read',
        'classification:collection:read',
        'exam_student:sub:read',
    ])]
    private string $name;

    #[ORM\Column(type: 'string', length: 25)]
    #[Groups([
        'session_type:collection:read',
        'classification:collection:read',
        'exam_student:sub:read',
    ])]
    private string $code;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
