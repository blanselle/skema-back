<?php

declare(strict_types=1);

namespace App\Entity\OralTest;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Constants\OralTest\OralTestStudentWorkflowStateConstants;
use App\Entity\AutomaticStudentOnPostInterface;
use App\Entity\Student;
use App\Entity\Traits\DateTrait;
use App\Repository\OralTest\OralTestStudentRepository;
use App\Validator\OralTestStudent\UniqueAccordingToState;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OralTestStudentRepository::class)]
#[ORM\Table(name: 'oral_test_oral_test_student')]
#[ORM\Index(columns: ['state'], name: 'oral_test_oral_test_student_state_idx')]
#[ORM\UniqueConstraint(
    name: 'oral_test_oral_test_student_unique_index',
    columns: ['student_id'],
    options: ['where' => '(state <> \''.OralTestStudentWorkflowStateConstants::REJECTED.'\')']
)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => ['groups' => ['oralTestStudent:collection:read']],
            'denormalization_context' => ['groups' => ['oralTestStudent:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
        'get' => [
            'security' => 'is_granted("ROLE_CANDIDATE")'
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['oralTestStudent:collection:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getStudent().getUser() == user'
        ],
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['campusOralDay' => 'exact', 'student' => 'exact', 'state' => 'exact'])]
#[Assert\Sequentially([
    new UniqueAccordingToState(),
])]
class OralTestStudent implements AutomaticStudentOnPostInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'oralTestStudent:collection:read',
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: CampusOralDay::class, inversedBy: 'oralTestStudents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'oralTestStudent:collection:read',
        'oralTestStudent:collection:write',
    ])]
    private CampusOralDay $campusOralDay;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'oralTestStudents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'oralTestStudent:collection:read',
    ])]
    private ?Student $student = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        'oralTestStudent:collection:read',
    ])]
    private string $state = OralTestStudentWorkflowStateConstants::WAITING_FOR_TREATMENT;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): OralTestStudent
    {
        $this->id = $id;

        return $this;
    }

    public function getCampusOralDay(): CampusOralDay
    {
        return $this->campusOralDay;
    }

    public function setCampusOralDay(CampusOralDay $campusOralDay): OralTestStudent
    {
        $this->campusOralDay = $campusOralDay;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): OralTestStudent
    {
        $this->student = $student;
        if(!$student->getOralTestStudents()->contains($this)) {
            $student->addOralTestStudent($this);
        }

        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): OralTestStudent
    {
        $this->state = $state;
        return $this;
    }
}