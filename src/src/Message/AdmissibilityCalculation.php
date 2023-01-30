<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

class AdmissibilityCalculation
{
    public function __construct(private string $message, private int $calculatorId, private Uuid $userId) {}

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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