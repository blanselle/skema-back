<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Loggable\History;
use App\Entity\Notification\Notification;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Action\User\GetMe;
use App\Constants\User\UserRoleConstants;
use App\Entity\Traits\DateTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[Gedmo\Loggable(logEntryClass: History::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['user:collection:read'],
            ],
            'security' => 'is_granted("ROLE_COORDINATOR")',
        ],
        'post' => [
            'normalization_context' => ['groups' => ['user:collection:read']],
            'denormalization_context' => ['groups' => ['user:collection:write']],
            'validation_groups' => ['user:validation'],
        ],
        'get_me' => [
            'method' => 'GET',
            'path' => '/users/me',
            'controller' => GetMe::class,
            'normalization_context' => [
                'groups' => ['user:item:read-me'],
            ],
            'security' => 'is_granted("ROLE_CANDIDATE")',
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['user:item:read'],
            ],
            'security' => 'is_granted("ROLE_CANDIDATE") or object == user'
        ],
        'put' => [
            'normalization_context' => ['groups' => ['user:item:read']],
            'denormalization_context' => [
                'groups' => ['user:item:write'],
            ],
            'security' => 'is_granted("ROLE_CANDIDATE") or object == user',
            'validation_groups' => ['user:validation'],
        ],
    ],
)]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DateTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:item:read-me',
    ])]
    private Uuid $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
    ])]
    #[Assert\Email(
        message: 'The email {{ value }} is not a valid email.',
        groups: ['user:validation'],
    )]
    #[Gedmo\Versioned]
    private string $email;

    #[ORM\Column(type: 'array')]
    #[Groups([
        'user:item:read',
        'user:collection:read',
        'user:item:read-me',
    ])]
    #[Assert\NotBlank(message: 'The role is mandatory', groups: ['user:validation'])]
    #[Assert\Choice(
        callback: [UserRoleConstants::class, 'getConsts'],
        multiple: true,
        message: 'The type {{ value }} is not in {{ choices }}',
        groups: ['user:validation']
    )]
    private array $roles = [UserRoleConstants::ROLE_CANDIDATE];

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups([
        'user:item:write',
        'user:collection:write'
    ])]
    #[Assert\Regex(
        "/(?=^.{8,}$)(?=.*\d)(?=.*[!@#$%^&*]+)(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
        message: "Le mot de passe ne respecte pas les conditions de sécurité: 1 majuscule, 1 minuscule, 1 chiffre, 1 caratère spécial, au moins 8 caractères !",
        groups: ['user:validation']
    )]
    private ?string $plainPassword;

    #[ORM\Column(type: 'string')]
    #[Gedmo\Versioned]
    private string $password;

    #[ORM\OneToOne(inversedBy: 'user', targetEntity: Student::class, cascade: ['persist', 'remove'])]
    #[Assert\Valid(groups: ['user:validation'])]
    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:read',
        'user:collection:write',
        'user:item:read-me',
    ])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Student $student = null;

    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:write',
        'user:item:read-me',
    ])]
    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull(groups: ['user:validation'])]
    #[Gedmo\Versioned]
    private string $firstName;

    #[Groups([
        'user:item:read',
        'user:item:write',
        'user:collection:write',
        'user:item:read-me',
    ])]
    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotNull(groups: ['user:validation'])]
    #[Gedmo\Versioned]
    private string $lastName;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Notification::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Collection $notifications;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('%s - %s',
            $this->firstName,
            $this->lastName
        );
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = strtolower($email);

        return $this;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles(), true);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;
        $student->setUser($this);

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setSender($this);
        }

        return $this;
    }
}
