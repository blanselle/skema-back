<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Entity\Notification\Notification;
use Doctrine\ORM\EntityManagerInterface;

class DbNotification extends AbstractNotification
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function send(Notification $notification): void
    {
        $this->em->persist($notification);
        $this->em->flush();
    }
}
