<?php

namespace App\Model\Student;

class ExportChoiceModel
{
    private string $value; // Query builder field name
    private string $label; // The label to display in file
    private int $position; // The column number
    private bool $intern; // Set to "false" to retrieve the value from another query
    private bool $manual; // Set to true if the value is set manually (ie; without query)

    public function __construct(string $value, string $label, int $position = 1, bool $intern = true, bool $manual = false)
    {
        $this->value = $value;
        $this->label = $label;
        $this->position = $position;
        $this->intern = $intern;
        $this->manual = $manual;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): ExportChoiceModel
    {
        $this->value = $value;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): ExportChoiceModel
    {
        $this->label = $label;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): ExportChoiceModel
    {
        $this->position = $position;

        return $this;
    }

    public function isIntern(): bool
    {
        return $this->intern;
    }

    public function setIntern(bool $intern): ExportChoiceModel
    {
        $this->intern = $intern;

        return $this;
    }

    public function isManual(): bool
    {
        return $this->manual;
    }

    public function setManual(bool $manual): ExportChoiceModel
    {
        $this->manual = $manual;

        return $this;
    }
}