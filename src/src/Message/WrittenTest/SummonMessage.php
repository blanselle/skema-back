<?php

declare(strict_types=1);

namespace App\Message\WrittenTest;

class SummonMessage
{
    public function __construct(private int $examStudentId) {}

    public function getExamStudentId(): int
    {
        return $this->examStudentId;
    }

    public function setExamStudentId(int $examStudentId): self
    {
        $this->examStudentId = $examStudentId;

        return $this;
    }
}
