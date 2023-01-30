<?php

declare(strict_types=1);

namespace App\Message;

class MulticastMessage
{
    private array $studentIds = [];

    private ?string $senderId = null;

    private ?string $subject = null;

    private ?string $content = null;

    private ?array $roleSender = [];

    public function getStudentIds(): array
    {
        return $this->studentIds;
    }

    public function setStudentIds(array $studentIds): self
    {
        $this->studentIds = $studentIds;

        return $this;
    }

    public function getSenderId(): ?string
    {
        return $this->senderId;
    }

    public function setSenderId(?string $senderId): self
    {
        $this->senderId = $senderId;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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
}
