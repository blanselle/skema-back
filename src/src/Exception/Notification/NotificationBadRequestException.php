<?php

declare(strict_types=1);

namespace App\Exception\Notification;

use Exception;

class NotificationBadRequestException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            message: 'Notification bad request',
            code: 500,
        );
    }
}
