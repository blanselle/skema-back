<?php

declare(strict_types=1);

namespace App\Constants\Mail;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class MailConstants implements ConstantsInterface
{
    public const MAIL_SC = 'email_sc';
    public const MAIL_ADMIN = 'email';
    public const MAIL_REJECT_KEY = 'MAIL_REJECT';

    private function __construct()
    {
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
