<?php

namespace App\Model\Student;

class ExportStudentListModel
{
    private array $columns = [];

    private ?string $identifier = null;

    private ?string $lastname = null;

    private ?string $state = null;

    private ?string $mediaCode = null;

    private ?string $media = null;

    private bool $intern = false;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): ExportStudentListModel
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): ExportStudentListModel
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): ExportStudentListModel
    {
        $this->state = $state;

        return $this;
    }

    public function getMediaCode(): ?string
    {
        return $this->mediaCode;
    }

    public function setMediaCode(?string $mediaCode): ExportStudentListModel
    {
        $this->mediaCode = $mediaCode;

        return $this;
    }

    public function getMedia(): ?string
    {
        return $this->media;
    }

    public function setMedia(?string $media): ExportStudentListModel
    {
        $this->media = $media;

        return $this;
    }

    public function isIntern(): bool
    {
        return $this->intern;
    }

    public function setIntern(?bool $intern): ExportStudentListModel
    {
        $this->intern = $intern?? false;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function setColumns(array $columns): ExportStudentListModel
    {
        $this->columns = $columns;

        return $this;
    }
}