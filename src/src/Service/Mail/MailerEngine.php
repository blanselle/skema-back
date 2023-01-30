<?php

declare(strict_types=1);

namespace App\Service\Mail;

use App\Constants\Mail\MailConstants;
use App\Message\MailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;

class MailerEngine
{
    public function __construct(
        private MessageBusInterface $bus,
        private EmailFromConfig $emailFromConfig,
    ) {
    }

    public function dispatch(array $to, string $subject, string $body, ?string $from = null): void
    {
        if (null === $from) {
            $from = $this->emailFromConfig->get(MailConstants::MAIL_SC);
        }

        $email = (new Email())
            ->from($from)
            ->to(...$to)
            ->subject($subject)
            ->html($body);

        $mailMessage = new MailMessage($email);
        $this->bus->dispatch($mailMessage);
    }
}
