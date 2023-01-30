<?php

declare(strict_types=1);

namespace App\Model\Dashboard;

class Row
{
    private string $label;

    private ?string $key = null;

    private array $values = [];

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(?string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function addValue(int $value): self
    {
        $this->values[] = $value;

        return $this;
    }

    public function setValues(array $values): self
    {
        $this->values = $values;

        return $this;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getTotal(): int
    {
        return intval(array_sum($this->values));
    }
}
