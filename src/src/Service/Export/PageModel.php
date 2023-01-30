<?php

declare(strict_types=1);

namespace App\Service\Export;

/**
 * Model page of Excel
 */
class PageModel
{
    private string $title;
    private string $name;
    private array $headers = [];
    private array $rows = [];
    private int $rowHeight = 20;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $header): self
    {
        $this->headers[] = $header;

        return $this;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function setRows(array $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    public function addRow(array $row): self
    {
        $this->rows[] = $row;

        return $this;
    }

    public function getWidth(): int
    {
        return count($this->getHeaders());
    }

    public function getRowHeight(): int
    {
        return $this->rowHeight;
    }

    public function setRowHeight(int $rowHeight): self
    {
        $this->rowHeight = $rowHeight;

        return $this;
    }
}
