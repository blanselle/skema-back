<?php

declare(strict_types=1);

namespace App\Message;

class NotificationsAdmissibilityResultMessage
{
    private string $receiverIdentifier;

    public function getReceiverIdentifier(): string
    {
        return $this->receiverIdentifier;
    }

    public function setReceiverIdentifier(string $receiverIdentifier): self
    {
        $this->receiverIdentifier = $receiverIdentifier;

        return $this;
    }
}
