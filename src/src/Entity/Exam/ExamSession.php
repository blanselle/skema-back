<?php

declare(strict_types=1);

namespace App\Entity\Exam;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Constants\Exam\ExamConditionConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Campus;
use App\Entity\Payment\Order;
use App\Entity\Traits\DateTrait;
use App\Filter\ExamSession\ActiveExamSessionFilter;
use App\Repository\Exam\ExamSessionRepository;
use App\Validator\ExamSession\ExamSessionDate;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['session:collection:read']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['session:collection:read']],
            'denormalization_context' => ['groups' => ['session:collection:write']],
            'validation_groups' => ['session:write'],
            'security' => 'is_granted("ROLE_CANDIDATE")'
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['session:collection:read']],
            'denormalization_context' => ['groups' => ['session:collection:put']],
            'validation_groups' => ['session:write'],
            'security' => "is_granted('ROLE_CANDIDATE') and object.getType() == '".ExamSessionTypeConstants::TYPE_OUTSIDE."' and object.getExamStudents().count() > 0 and user == object.getExamStudents()[0].getStudent().getUser()",
        ],
    ],
)]
#[ApiFilter(filterClass: ActiveExamSessionFilter::class, properties: [
    'active' => 'exact',
])]
#[ApiFilter(SearchFilter::class, properties: ['campus.id' => 'exact', 'examClassification.id' => 'exact'])]
#[ORM\Entity(repositoryClass: ExamSessionRepository::class)]
class ExamSession
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]    
    #[Groups([
        'exam_student:sub:read',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    #[Assert\Expression(
        "this.getType() === '".ExamSessionTypeConstants::TYPE_OUTSIDE."' || null !== this.getPrice() && null === this.getPriceLink() || null === this.getPrice() && null !== this.getPriceLink()",
        message: 'Le tarif ou le lien de paiement doit être complété (l\'un ou l\'autre)'
    )]
    #[Groups([
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private ?string $price = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Expression(
        "this.getType() === '".ExamSessionTypeConstants::TYPE_OUTSIDE."' || null !== this.getPrice() && null === this.getPriceLink() || null === this.getPrice() && null !== this.getPriceLink()",
        message: 'Le tarif ou le lien de paiement doit être complété (l\'un ou l\'autre)'
    )]
    #[Groups([
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private ?string $priceLink = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups([
        'session:collection:read',
        'session:collection:write',
        'session:collection:put',
        'exam_student:sub:read',
        'order:collection:read',
    ])]
    #[Assert\NotNull(groups: ['session:write'])]
    #[ExamSessionDate(groups: ['session:write'])]
    private DateTimeInterface $dateStart;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression(
        "this.getExamClassification().getExamCondition().getName() !== '".ExamConditionConstants::CONDITION_ONLINE."' ||
        this.getExamClassification().getExamCondition().getName() === '".ExamConditionConstants::CONDITION_ONLINE."' && null !== this.getDateEnd()",
        message: 'La date de fin de session est obligatoire.'
    )]
    #[Assert\Expression(
        "null == this.getDateEnd() || this.getDateStart() <= this.getDateEnd()",
        message:"La date de fin ne doit pas être antérieure à la date de début"
    )]
    #[Groups([
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private ?DateTimeInterface $dateEnd = null;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'examSessions')]
    #[Assert\Expression(
        "this.getExamClassification().getExamCondition().getName() === '".ExamConditionConstants::CONDITION_ONLINE."' && null === this.getCampus() ||
        this.getExamClassification().getExamCondition().getName() !== '".ExamConditionConstants::CONDITION_ONLINE."'",
        message: 'Un campus ne peut être assigné pour une session en ligne'
    )]
    #[Assert\Expression(
        "this.getType() === '".ExamSessionTypeConstants::TYPE_OUTSIDE."' || 
        (this.getExamClassification().getExamCondition().getName() === '".ExamConditionConstants::CONDITION_ONLINE."' ||
        this.getExamClassification().getExamCondition().getName() !== '".ExamConditionConstants::CONDITION_ONLINE."' && null !== this.getCampus())",
        message: 'Un campus doit être assigné pour une session en présentiel'
    )]
    #[Groups([
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private ?Campus $campus = null;

    #[ORM\ManyToMany(targetEntity: ExamRoom::class)]
    #[Groups([
        'session:collection:read',
    ])]
    private Collection $examRooms;

    #[ORM\Column(type: 'integer')]
    #[Assert\Expression(
        "this.getType() === '".ExamSessionTypeConstants::TYPE_INSIDE."' || this.getType() === '".ExamSessionTypeConstants::TYPE_OUTSIDE."'  && value === 1",
        message: 'Pour une session de type '.ExamSessionTypeConstants::TYPE_OUTSIDE.', le nombre de place doit être de 1.'
    )]
    #[Groups([
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private int $numberOfPlaces = 1;

    #[ORM\OneToMany(mappedBy: 'examSession', targetEntity: ExamStudent::class, orphanRemoval: true)]
    #[Groups([
        'session:collection:read',
    ])]
    private Collection $examStudents;

    #[ORM\ManyToOne(targetEntity: ExamClassification::class, inversedBy: 'examSessions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'session:collection:read',
        'session:collection:write',
        'exam_student:sub:read',
        'order:collection:read',
    ])]
    #[Assert\NotNull(groups: ['session:write'])]
    private ExamClassification $examClassification;

    #[ORM\Column(type: 'boolean')]
    #[Groups([
        'session:collection:read',
    ])]
    private bool $distributed = false;

    #[ORM\Column(type: 'string', length: 15)]
    #[Assert\Choice(
        callback: [ExamSessionTypeConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
    )]
    #[Groups([
        'session:collection:read',
        'exam_student:sub:read',
    ])]
    private string $type = ExamSessionTypeConstants::TYPE_OUTSIDE;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'session:collection:read',
        'session:collection:write',
        'session:collection:put',
        'exam_student:sub:read',
    ])]
    private ?string $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        'session:collection:read',
        'session:collection:write',
        'session:collection:put',
        'exam_student:sub:read',
    ])]
    private ?string $fnege;

    #[ORM\OneToMany(mappedBy: 'examSession', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->examRooms = new ArrayCollection();
        $this->examStudents = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceLink(): ?string
    {
        return $this->priceLink;
    }

    public function setPriceLink(?string $priceLink): self
    {
        $this->priceLink = $priceLink;

        return $this;
    }

    public function getDateStart(): \DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function setExamRooms(Collection $examRooms): self
    {
        $this->examRooms = $examRooms;

        return $this;
    }

    public function getExamRooms(): Collection
    {
        return $this->examRooms;
    }

    public function addExamRoom(ExamRoom $examRoom): self
    {
        if (!$this->examRooms->contains($examRoom)) {
            $this->examRooms[] = $examRoom;
        }

        return $this;
    }

    public function removeExamRoom(ExamRoom $examRoom): self
    {
        $this->examRooms->removeElement($examRoom);

        return $this;
    }

    public function getNumberOfPlaces(): ?int
    {
        return $this->numberOfPlaces;
    }

    public function setNumberOfPlaces(int $numberOfPlaces): self
    {
        $this->numberOfPlaces = $numberOfPlaces;

        return $this;
    }

    public function getExamStudents(): Collection
    {
        return $this->examStudents;
    }

    public function addExamStudent(ExamStudent $examStudent): self
    {
        if (!$this->examStudents->contains($examStudent)) {
            $this->examStudents[] = $examStudent;
            $examStudent->setExamSession($this);
        }

        return $this;
    }

    public function removeExamStudent(ExamStudent $examStudent): self
    {
        if ($this->examStudents->removeElement($examStudent)) {
            // set the owning side to null (unless already changed)
            if ($examStudent->getExamSession() === $this) {
                $examStudent->setExamSession(null);
            }
        }

        return $this;
    }

    public function getExamClassification(): ?ExamClassification
    {
        return $this->examClassification;
    }

    public function setExamClassification(?ExamClassification $examClassification): self
    {
        $this->examClassification = $examClassification;

        return $this;
    }

    /**
     * La repartition a été effectuée
     */
    public function isDistributed(): bool
    {
        return $this->distributed;
    }

    public function setDistributed(bool $distributed): self
    {
        $this->distributed = $distributed;

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

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Identifiant pour les BCE
     */
    public function getFnege(): ?string
    {
        return $this->fnege;
    }

    public function setFnege(?string $fnege): self
    {
        $this->fnege = $fnege;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setExamSession($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getExamSession() === $this) {
                $order->setExamSession(null);
            }
        }

        return $this;
    }
}
