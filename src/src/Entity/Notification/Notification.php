<?php

declare(strict_types=1);

namespace App\Entity\Notification;

use ApiPlatform\Core\Annotation\ApiFilter;
use App\Entity\Loggable\History;
use App\Entity\User;
use App\Entity\ProgramChannel;
use App\Entity\Traits\DateTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\Notification\NotificationRepository;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['notification:collection:read']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            "order" => ['createdAt' => 'DESC'],
        ],
        'post' => [
            'normalization_context' => ['groups' => ['notification:collection:read']],
            'denormalization_context' => ['groups' => ['notification:collection:write']],
            'security' => 'is_granted("ROLE_CANDIDATE")',
            'validation_groups' => ['api'],
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['notification:item:read']],
            'security' => 'is_granted("ROLE_CANDIDATE") and (object.getReceiver() == user or object.getSender() == user)',
        ],
        'put' => [
            'normalization_context' => ['groups' => ['notification:item:read']],
            'denormalization_context' => ['groups' => ['notification:item:write']],
            'security' => 'is_granted("ROLE_CANDIDATE") and object.getReceiver() == user',
            'validation_groups' => ['api'],
        ],
    ],
    attributes: ["pagination_enabled" => false]
)]
#[ApiFilter(filterClass: SearchFilter::class, properties: [
    'read' => 'exact',
])]
class Notification
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    #[Groups([
        'notification:item:read',
        'notification:collection:read',
        'notification:collection:write',
    ])]
    private ?Notification $parent = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups([
        'notification:item:read',
        'notification:collection:read',
    ])]
    private ?User $sender = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $receiver = null;

    #[ORM\Column(type: 'array', nullable: true)]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le sujet ne peut pas excéder {{ limit }} caractères',
        groups: ['api', 'bo'],
    )]
    #[Groups([
        'notification:item:read',
        'notification:collection:read',
        'notification:collection:write',
    ])]
    #[Gedmo\Versioned]
    #[Assert\NotNull(groups: ['api'])]
    #[Assert\NotBlank(groups: ['api'])]
    private ?string $subject = null;

    #[ORM\Column(type: 'text')]
    #[Groups([
        'notification:item:read',
        'notification:collection:read',
        'notification:collection:write',
    ])]
    #[Assert\NotNull(groups: ['api'])]
    #[Assert\NotBlank(groups: ['api'])]
    private ?string $content = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $identifier = null; // Numéro du student

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    #[Groups([
        'notification:item:read',
        'notification:collection:read',
        'notification:item:write'
    ])]
    #[Assert\NotNull(groups: ['api', 'bo'])]
    private bool $read = false;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[ORM\OrderBy(['createdAt' => 'desc'])]
    private Collection $replies;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToMany(targetEntity: ProgramChannel::class)]
    private Collection $programChannels;

    #[ORM\ManyToOne(targetEntity: NotificationTemplate::class)]
    private ?NotificationTemplate $notificationTemplate = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $private = false;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $roleSender = []; 

    public function __construct()
    {
        $this->read = false;
        $this->replies = new ArrayCollection();
        $this->programChannels = new ArrayCollection();
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * If there is no sender we consider it is the service concours
     *
     * @return User|null
     */
    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getRead(): bool
    {
        return $this->read;
    }

    public function setRead(bool $read): self
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function addReply(self $reply): self
    {
        if (!$this->replies->contains($reply)) {
            $this->replies[] = $reply;
            $reply->setParent($this);
        }

        return $this;
    }

    public function removeReply(self $reply): self
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getParent() === $this) {
                $reply->setParent(null);
            }
        }

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getProgramChannels(): Collection
    {
        return $this->programChannels;
    }

    public function setProgramChannels(Collection $programChannels): self
    {
        $this->programChannels = $programChannels;

        return $this;
    }

    public function addProgramChannel(ProgramChannel $programChannel): self
    {
        if (!$this->programChannels->contains($programChannel)) {
            $this->programChannels[] = $programChannel;
        }

        return $this;
    }

    public function removeProgramChannel(ProgramChannel $programChannel): self
    {
        $this->programChannels->removeElement($programChannel);

        return $this;
    }

    public function getNotificationTemplate(): ?NotificationTemplate
    {
        return $this->notificationTemplate;
    }

    public function setNotificationTemplate(?NotificationTemplate $notificationTemplate): self
    {
        $this->notificationTemplate = $notificationTemplate;

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }

    public function getRoleSender(): ?array
    {
        return $this->roleSender;
    }

    public function setRoleSender(?array $roleSender): self
    {
        $this->roleSender = $roleSender;

        return $this;
    }
}
