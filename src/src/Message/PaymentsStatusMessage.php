<?php

namespace App\Message;

class PaymentsStatusMessage
{
    public function __construct(private array $paymentsId) {}

    public function getPaymentsId(): array
    {
        return $this->paymentsId;
    }
}