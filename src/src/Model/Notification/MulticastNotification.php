<?php

namespace App\Model\Notification;

class MulticastNotification
{
    private ?string $identifier = null;
    private ?string $lastname = null;
    private ?string $state = null;
    private ?string $mediaCode = null;
    private ?string $media = null;
    private ?string $subject = null;
    private ?string $content = null;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): MulticastNotification
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): MulticastNotification
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): MulticastNotification
    {
        $this->state = $state;
        return $this;
    }

    public function getMediaCode(): ?string
    {
        return $this->mediaCode;
    }

    public function setMediaCode(?string $mediaCode): MulticastNotification
    {
        $this->mediaCode = $mediaCode;
        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): MulticastNotification
    {
        $this->media = $media;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): MulticastNotification
    {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): MulticastNotification
    {
        $this->content = $content;
        return $this;
    }
}