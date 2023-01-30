<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

class EligibleStudents
{
    public function __construct(private array $programChannelIds, private float $score, private int $calculatorId, private Uuid $userId) {}

    public function getProgramChannelIds(): array
    {
        return $this->programChannelIds;
    }

    public function setProgramChannelIds(array $programChannelIds): self
    {
        $this->programChannelIds = $programChannelIds;

        return $this;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getCalculatorId(): int
    {
        return $this->calculatorId;
    }

    public function setCalculatorId(int $id): self
    {
        $this->calculatorId = $id;

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