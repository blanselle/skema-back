<?php

declare(strict_types=1);

namespace App\Message\WrittenTest;

use Symfony\Component\Uid\Uuid;

class SummonsMessage
{
    public function __construct(
        private int $examClassificationId,
        private Uuid $userId,
    ) {}

    public function getExamClassificationId(): int
    {
        return $this->examClassificationId;
    }

    public function setExamClassificationId(int $examClassificationId): self
    {
        $this->examClassificationId = $examClassificationId;

        return $this;
    }

    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    public function setUserId(Uuid $userId): self
    {
        $this->userId = $userId;

        return $this;
    }
}
