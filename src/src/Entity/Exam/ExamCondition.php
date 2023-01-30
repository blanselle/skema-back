<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use App\Entity\Traits\DateTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
class ExamCondition
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
        'exam_student:sub:read',
    ])]
    private string $name;

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
}
