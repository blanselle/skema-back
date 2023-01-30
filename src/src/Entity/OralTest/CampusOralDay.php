<?php

namespace App\Entity\OralTest;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\OralTest\GetCollectionAvailable;
use App\Entity\Exam\ExamLanguage;
use App\Repository\OralTest\CampusOralDayRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: CampusOralDayRepository::class)]
#[ORM\UniqueConstraint(
    name: 'oral_test_campus_oral_day_unique_idx',
    columns: ['configuration_id', 'first_language_id', 'second_language_id', 'date'],
)]
#[ORM\Table(name: 'oral_test_campus_oral_day')]
#[ApiResource(
    collectionOperations: [
        'get_collection_available' => [
            'method' => 'GET',
            'path' => '/campus_oral_days/available_slots',
            'controller' => GetCollectionAvailable::class,
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'normalization_context' => [
                'groups' => ['slot:collection:read'],
            ],
            'openapi_context' => [
                'summary' => 'Get available slots.',
                'description' => 'get collection of available slots depends on student program channel and first and second exam language.',
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'normalization_context' => [
                'groups' => ['slot:item:read'],
            ],
        ],
    ]
)]
class CampusOralDay
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'slot:collection:read',
        'oralTestStudent:collection:read'
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'campusOralDays')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    #[Groups([
        'slot:collection:read',
        'oralTestStudent:collection:read'
    ])]
    private ?CampusOralDayConfiguration $configuration = null;

    #[ORM\Column]
    #[Groups([
        'slot:collection:read',
        'oralTestStudent:collection:read',
    ])]
    private ?DateTimeImmutable $date = null;

    #[ORM\ManyToOne]
    #[MaxDepth(1)]
    private ?ExamLanguage $firstLanguage = null;

    #[ORM\ManyToOne]
    #[MaxDepth(1)]
    private ?ExamLanguage $secondLanguage = null;

    #[ORM\Column]
    private int $nbOfReservedPlaces = 0;

    #[ORM\Column]
    private int $nbOfAvailablePlaces = 0;

    #[ORM\OneToMany(mappedBy: 'campusOralDay', targetEntity: OralTestStudent::class, orphanRemoval: true)]
    private Collection $oralTestStudents;

    public function __construct()
    {
        $this->oralTestStudents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfiguration(): ?CampusOralDayConfiguration
    {
        return $this->configuration;
    }

    public function setConfiguration(?CampusOralDayConfiguration $configuration): self
    {
        $this->configuration = $configuration;
        if (null !== $configuration) {
            $configuration->addCampusOralDay($this);
        }

        return $this;
    }

    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFirstLanguage(): ?ExamLanguage
    {
        return $this->firstLanguage;
    }

    public function setFirstLanguage(?ExamLanguage $firstLanguage): self
    {
        $this->firstLanguage = $firstLanguage;

        return $this;
    }

    public function getSecondLanguage(): ?ExamLanguage
    {
        return $this->secondLanguage;
    }

    public function setSecondLanguage(?ExamLanguage $secondLanguage): self
    {
        $this->secondLanguage = $secondLanguage;

        return $this;
    }

    public function getNbOfReservedPlaces(): int
    {
        return $this->nbOfReservedPlaces;
    }

    public function setNbOfReservedPlaces(int $nbOfReservedPlaces): self
    {
        $this->nbOfReservedPlaces = $nbOfReservedPlaces;

        return $this;
    }

    public function getNbOfAvailablePlaces(): int
    {
        return $this->nbOfAvailablePlaces;
    }

    public function setNbOfAvailablePlaces(int $nbOfAvailablePlaces): self
    {
        $this->nbOfAvailablePlaces = $nbOfAvailablePlaces;

        return $this;
    }

    public function getOralTestStudents(): Collection
    {
        return $this->oralTestStudents;
    }

    public function setOralTestStudents(Collection $oralTestStudents): CampusOralDay
    {
        $this->oralTestStudents = $oralTestStudents;

        return $this;
    }

    public function addOralTestStudent(OralTestStudent $oralTestStudent): CampusOralDay
    {
        if (!$this->oralTestStudents->contains($oralTestStudent)) {
            $this->oralTestStudents[] = $oralTestStudent;
            $oralTestStudent->setCampusOralDay($this);
        }

        return $this;
    }

    public function removeOralTestStudent(OralTestStudent $oralTestStudent): CampusOralDay
    {
        $this->oralTestStudents->removeElement($oralTestStudent);

        return $this;
    }
}
