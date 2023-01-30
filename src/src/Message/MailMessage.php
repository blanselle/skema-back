<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Mime\Email;

class MailMessage
{
    public function __construct(private Email $email)
    {
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @param Email $email
     */
    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }
}
