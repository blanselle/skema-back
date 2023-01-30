<?php

namespace App\Entity\Loggable;

use App\Entity\Student;
use App\Repository\Loggable\HistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

#[ORM\Entity(repositoryClass: HistoryRepository::class)]
#[ORM\Table(name: 'loggable_history', options: ['row_format' => 'DYNAMIC'])]
#[ORM\Index(columns: ['object_class'], name: 'loggable_history_class_lookup_idx')]
#[ORM\Index(columns: ['logged_at'], name: 'loggable_history_date_lookup_idx')]
#[ORM\Index(columns: ['username'], name: 'loggable_history_username_lookup_idx')]
#[ORM\Index(columns: ['student_id'], name: 'loggable_history_student_lookup_idx')]
#[ORM\Index(columns: ['object_id', 'object_class', 'version'], name: 'loggable_history_version_lookup_idx')]
class History extends AbstractLogEntry
{
    #[ORM\Column(type: 'string', length: 255)]
    private string $type;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'histories')]
    #[ORM\JoinColumn(onDelete: "CASCADE")]
    private ?Student $student = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
}
