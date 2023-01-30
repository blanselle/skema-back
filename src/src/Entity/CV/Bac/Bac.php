<?php

declare(strict_types=1);

namespace App\Entity\CV\Bac;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Entity\CV\Cv;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Entity\Traits\DateTrait;
use App\Interface\Admissibility\CvCalculationInterface;
use App\Interface\DetailInterface;
use App\Repository\CV\Bac\BacRepository;
use App\Validator as Validator;
use App\Validator\Bac\BacCvValidated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BacRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'post' => [
            'normalization_context' => ['groups' => ['bac:collection:read']],
            'denormalization_context' => ['groups' => ['bac:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'validation_groups' => ['api'],
            'security_post_denormalize' => 'is_granted("create", object)',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['bac:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getCv().getStudent().getUser()',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['bac:item:read']],
            'denormalization_context' => ['groups' => ['bac:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and user === object.getCv().getStudent().getUser()',
            'validation_groups' => ['api'],
            'security_post_denormalize' => 'is_granted("edit", {\'original\': previous_object, \'object\': object})',
        ],
    ],
)]
/**
 * Info sur le bac d'un etudiant
 */
#[BacCvValidated(groups: ['api'])]
class Bac implements CvCalculationInterface, DetailInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Assert\NotBlank(groups: ['api', 'bo', 'cv:validation'])]
    #[Assert\Positive(groups: ['api', 'bo', 'cv:validation'])]
    #[Assert\NotNull(groups: ['api', 'bo', 'cv:validation'])]
    #[Assert\GreaterThan(999, groups: ['api', 'bo', 'cv:validation'], message: 'L\'année doit être composée de quatre chiffres')]
    #[Assert\LessThan(10000, groups: ['api', 'bo', 'cv:validation'], message: 'L\'année doit être composée de quatre chiffres')]
    #[Validator\Parameter\LessThanParameter(
        parameterName: 'anneeConcours',
        programChannelId: 'this.getCv().getStudent().getProgramChannel().getId()',
        groups: ['api', 'bo', 'cv:validation'],
        message: 'Vous devez avoir obtenu le baccalauréat avant {{ parameter }}'
    )]
    #[Validator\Cv\BacYear(
        groups: ['api', 'bo', 'cv:validation'], 
    )]
    #[Gedmo\Versioned]
    private ?int $rewardedYear = null;

    #[ORM\ManyToOne(targetEntity: BacDistinction::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(groups: ['api', 'bo', 'cv:validation'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Gedmo\Versioned]
    private ?BacDistinction $bacDistinction = null;

    #[ORM\ManyToOne(targetEntity: BacChannel::class)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Assert\NotNull(groups: ['api', 'bo', 'cv:validation'])]
    #[Gedmo\Versioned]
    private ?BacChannel $bacChannel = null;

    #[ORM\ManyToOne(targetEntity: BacOption::class)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Validator\Bac\BacOption(groups: ['api', 'cv:validation'])]
    #[Gedmo\Versioned]
    private ?BacOption $bacOption = null;

    #[ORM\OneToOne(inversedBy: 'bac', targetEntity: Cv::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Validator\ExpectedUser(
        expression: "object.getStudent().getUser()",
        groups: ['api', 'cv:validation'],
    )]
    #[Groups([
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    private ?Cv $cv = null;

    #[ORM\OneToOne(targetEntity: Media::class, cascade: ['persist'])]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Assert\Valid]
    private ?Media $media = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Validator\RequiredField(
        expression: 'this.getBacChannel() !== null && this.getBacChannel().getNeedDetail()',
        nullValues: [null, ''],
        groups: ['api', 'bo', 'cv:validation'],
        message: 'Le champs détail est obligatoire',
    )]
    #[Validator\RequiredFieldReverse(
        expression: 'this.getBacChannel() !== null && this.getBacChannel().getNeedDetail()',
        nullValues: [null, ''],
        groups: ['detail-to-null'],
    )]
    #[Gedmo\Versioned]
    private ?string $detail = null;

    #[ORM\ManyToMany(targetEntity: BacType::class)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Assert\NotNull(groups:['api', 'bo', 'cv:validation'])]
    #[Validator\Bac\BacTypeCount(
        groups: ['api', 'bo', 'cv:validation'],
    )]
    #[Validator\ExpressionOnCollection(
        'item.getBacChannel() === this.getBacChannel()',
        groups: ['api', 'bo', 'cv:validation'],
    )]
    private Collection $bacTypes;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Groups([
        'cv:item:read',
        'cv:collection:read',
        'bac:item:read',
        'bac:item:write',
        'bac:collection:write',
        'bac:collection:read',
    ])]
    #[Assert\Length(max: 50, groups: ['api', 'bo', 'cv:validation'])]
    #[Gedmo\Versioned]
    private ?string $ine;

    public function __construct()
    {
        $this->bacTypes = new ArrayCollection();
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

    public function getRewardedYear(): ?int
    {
        return $this->rewardedYear;
    }

    public function setRewardedYear(?int $rewardedYear): self
    {
        $this->rewardedYear = $rewardedYear;

        return $this;
    }

    public function getBacDistinction(): ?BacDistinction
    {
        return $this->bacDistinction;
    }

    public function setBacDistinction(?BacDistinction $bacDistinction): self
    {
        $this->bacDistinction = $bacDistinction;

        return $this;
    }

    public function getBacChannel(): ?BacChannel
    {
        return $this->bacChannel;
    }

    public function setBacChannel(?BacChannel $bacChannel): self
    {
        $this->bacChannel = $bacChannel;

        return $this;
    }

    public function getBacOption(): ?BacOption
    {
        return $this->bacOption;
    }

    public function setBacOption(?BacOption $bacOption): self
    {
        $this->bacOption = $bacOption;

        return $this;
    }

    public function getCv(): ?Cv
    {
        return $this->cv;
    }

    public function setCv(?Cv $cv): self
    {
        $this->cv = $cv;
        if(null !== $cv && $cv->getBac() !== $this) {
            $cv->setBac($this);
        }

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

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(?string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Liste des types de bac. ex: Math, SI
     * En bac général, après 2021, ya 2 type de bac
     */
    public function getBacTypes(): Collection
    {
        return $this->bacTypes;
    }

    public function addBacType(BacType $bacType): self
    {
        if (!$this->bacTypes->contains($bacType)) {
            $this->bacTypes[] = $bacType;
        }

        return $this;
    }

    public function removeBacType(BacType $bacType): self
    {
        $this->bacTypes->removeElement($bacType);

        return $this;
    }

    public function setBacTypes(Collection $bacTypes): self
    {
        foreach($bacTypes as $bacType) {
            $this->addBacType($bacType);
        }

        return $this;
    }

    public function getIne(): ?string
    {
        return $this->ine;
    }

    public function setIne(?string $ine): self
    {
        $this->ine = $ine;

        return $this;
    }
}
