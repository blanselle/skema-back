<?php

namespace App\Entity\Exam;

use App\Entity\Media;
use App\Entity\Student;
use App\Repository\Exam\ExamSummonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExamSummonRepository::class)]
class ExamSummon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'examStudent:item:read',
        'exam_student:sub:read',
    ])]
    private Media $media;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'examSummons')]
    #[ORM\JoinColumn(nullable: false)]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: ExamSession::class, inversedBy: 'examSummons')]
    #[ORM\JoinColumn(nullable: false)]
    private ExamSession $examSession;

    #[ORM\OneToOne(inversedBy: 'examSummon', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ExamStudent $examStudent;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getStudent(): Student
    {
        return $this->student;
    }

    public function setStudent(Student $student): self
    {
        $this->student = $student;

        return $this;
    }

    public function getExamSession(): ExamSession
    {
        return $this->examSession;
    }

    public function setExamSession(ExamSession $examSession): self
    {
        $this->examSession = $examSession;

        return $this;
    }

    public function getExamStudent(): ExamStudent
    {
        return $this->examStudent;
    }

    public function setExamStudent(ExamStudent $examStudent): self
    {
        $this->examStudent = $examStudent;

        return $this;
    }
}
