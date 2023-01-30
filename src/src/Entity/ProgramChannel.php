<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Entity\Admissibility\Param;
use App\Entity\Diploma\Diploma;
use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\SudokuConfiguration;
use App\Entity\Parameter\Parameter;
use App\Interface\Cv\KeyInterface;
use App\Repository\ProgramChannelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProgramChannelRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['programChannel:collection:read']],
            'openapi_context' => [
                'description' => 'Retrieves the collection of ProgramChannel resources. ProgramChannel is "voie de concours"',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['programChannel:item:read']],
            'openapi_context' => [
                'description' => 'Retrieves a ProgramChannel resources. ProgramChannel is "voie de concours"',
            ],
        ],
    ],
    attributes: ["security" => "is_granted('PUBLIC_ACCESS')"],
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'name' => 'partial'
])]
class ProgramChannel implements KeyInterface
{
    use Traits\DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'programChannel:item:read',
        'programChannel:collection:read',
        'user:item:read',
        'user:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull]
    #[Groups([
        'programChannel:item:read',
        'programChannel:collection:read',
        'user:item:read',
        'user:collection:read',
        'faq:collection:read',
        'faq:item:read',
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private string $name;

    #[ORM\OneToMany(mappedBy: 'programChannel', targetEntity: Student::class)]
    private Collection $students;

    #[ORM\ManyToOne(targetEntity: Program::class, inversedBy: 'programChannels')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    #[Groups([
        'programChannel:item:read',
        'programChannel:collection:read',
        'user:item:read',
        'user:collection:read',
    ])]
    private Program $program;

    #[ORM\ManyToMany(targetEntity: Diploma::class, mappedBy: 'programChannels')]
    #[Groups([
        'diplomaChannel:collection:read',
        'diplomaChannel:item:read',
    ])]
    private Collection $diplomas;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'programChannel:item:read',
        'programChannel:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Parameter::class, mappedBy: 'programChannels')]
    private Collection $parameters;

    #[ORM\OneToMany(mappedBy: 'programChannel', targetEntity: Param::class, orphanRemoval: true)]
    private Collection $admissibilityParams;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: true, options: ['default' => null])]
    #[Groups([
        'programChannel:item:read',
        'programChannel:collection:read',
        'bloc:collection:read',
        'bloc:item:read',
    ])]
    private ?string $key = null;
    
    #[ORM\Column(type: 'boolean', nullable: false)]
    private bool $intern = true;

    #[ORM\ManyToMany(targetEntity: ExamLanguage::class, mappedBy: 'programChannels')]
    private Collection $examLanguages;

    #[ORM\Column(type: 'integer', unique: false)]
    #[Gedmo\SortablePosition]
    private int $position = 1;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'programChannels')]
    private Collection $events;

    #[ORM\ManyToOne(inversedBy: 'programChannels')]
    private ?SudokuConfiguration $sudokuConfiguration = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->diplomas = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->admissibilityParams = new ArrayCollection();
        $this->examLanguages = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setProgramChannel($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student) && $student->getProgramChannel() === $this) {
            $student->setProgramChannel(null);
        }

        return $this;
    }

    public function getProgram(): ?Program
    {
        return $this->program;
    }

    public function setProgram(?Program $program): self
    {
        $this->program = $program;

        return $this;
    }

    public function getDiplomas(): Collection
    {
        return $this->diplomas;
    }

    public function addDiploma(Diploma $diploma): self
    {
        if (!$this->diplomas->contains($diploma)) {
            $this->diplomas[] = $diploma;
            $diploma->addProgramChannel($this);
        }

        return $this;
    }

    public function removeDiploma(Diploma $diploma): self
    {
        if ($this->diplomas->removeElement($diploma)) {
            $diploma->removeProgramChannel($this);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->addProgramChannel($this);
        }

        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            $parameter->removeProgramChannel($this);
        }
        return $this;
    }

    public function getIntern(): bool
    {
        return $this->intern;
    }

    public function setIntern(bool $intern): self
    {
        $this->intern = $intern;

        return $this;
    }

    public function getAdmissibilityParams(): Collection
    {
        return $this->admissibilityParams;
    }

    public function addAdmissibilityParam(Param $admissibilityParam): self
    {
        if (!$this->admissibilityParams->contains($admissibilityParam)) {
            $this->admissibilityParams[] = $admissibilityParam;
            $admissibilityParam->setProgramChannel($this);
        }

        return $this;
    }

    public function removeAdmissibilityParam(Param $admissibilityParam): self
    {
        if ($this->admissibilityParams->removeElement($admissibilityParam)) {
            // set the owning side to null (unless already changed)
            if ($admissibilityParam->getProgramChannel() === $this) {
                $admissibilityParam->setProgramChannel(null);
            }
        }

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

    /**
     * @return Collection<int, ExamLanguage>
     */
    public function getExamLanguages(): Collection
    {
        return $this->examLanguages;
    }

    public function addExamLanguage(ExamLanguage $examLanguage): self
    {
        if (!$this->examLanguages->contains($examLanguage)) {
            $this->examLanguages[] = $examLanguage;
            $examLanguage->addProgramChannel($this);
        }

        return $this;
    }

    public function removeExamLanguage(ExamLanguage $examLanguage): self
    {
        if ($this->examLanguages->removeElement($examLanguage)) {
            $examLanguage->removeProgramChannel($this);
        }

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getPositionKey(): string
    {
        return sprintf('p%04d', $this->getPosition());
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->addProgramChannel($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeProgramChannel($this);
        }

        return $this;
    }

    public function getSudokuConfiguration(): ?SudokuConfiguration
    {
        return $this->sudokuConfiguration;
    }

    public function setSudokuConfiguration(?SudokuConfiguration $sudokuConfiguration): self
    {
        $this->sudokuConfiguration = $sudokuConfiguration;

        return $this;
    }
}
