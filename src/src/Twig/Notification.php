<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Notification\NotificationCenter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Notification extends AbstractExtension
{
    public function __construct(
        private NotificationCenter $notificationCenter
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('CountNotifications', [$this, 'getCountUnreadNotifications']),
        ];
    }

    public function getCountUnreadNotifications(): int
    {
        return $this->notificationCenter->countUnreadNotificationsByUser();
    }
}
