<?php

declare(strict_types=1);

namespace App\Handler;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Message\MailMessage;

class MailHandler implements MessageHandlerInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function __invoke(MailMessage $mailMessage): void
    {
        $this->mailer->send($mailMessage->getEmail());
    }
}
