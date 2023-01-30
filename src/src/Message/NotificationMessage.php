<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\Uuid;

/**
 * Enveloppe pour Notification + code
 */
class NotificationMessage
{
    private ?int $parentId = null;
    private ?Uuid $senderId = null;
    private ?Uuid $receiverId = null;
    private array $roles = [];
    private ?string $subject = null;
    private ?string $content = null;
    private ?string $identifier = null;
    private ?string $comment = null;
    private array $programChannelsIds = [];
    private ?int $notificationTemplateId = null;
    private array $codes;
    private bool $private = false;
    private array $roleSender = [];
    private bool $sendGenericMail = true;

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    public function getSenderId(): ?Uuid
    {
        return $this->senderId;
    }

    public function setSenderId(?Uuid $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function getReceiverId(): ?Uuid
    {
        return $this->receiverId;
    }

    public function setReceiverId(?Uuid $receiverId): self
    {
        $this->receiverId = $receiverId;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getProgramChannelsIds(): array
    {
        return $this->programChannelsIds;
    }

    public function setProgramChannelsIds(array $programChannelsIds): self
    {
        $this->programChannelsIds = $programChannelsIds;

        return $this;
    }

    public function getNotificationTemplateId(): ?int
    {
        return $this->notificationTemplateId;
    }

    public function setNotificationTemplateId(?int $notificationTemplateId): self
    {
        $this->notificationTemplateId = $notificationTemplateId;

        return $this;
    }

    public function getCodes(): array
    {
        return $this->codes;
    }

    public function setCodes(array $codes): self
    {
        $this->codes = $codes;

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

    public function getRoleSender(): array
    {
        return $this->roleSender;
    }

    public function setRoleSender(array $roleSender): self
    {
        $this->roleSender = $roleSender;

        return $this;
    }

    public function getSendGenericMail(): bool
    {
        return $this->sendGenericMail;
    }

    public function setSendGenericMail(bool $sendGenericMail): self
    {
        $this->sendGenericMail = $sendGenericMail;

        return $this;
    }
}
