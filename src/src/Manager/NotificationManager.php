<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\Notification\Notification;
use App\Entity\Student;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Repository\BlocRepository;

class NotificationManager
{
    public function __construct(
        private BlocRepository $blocRepository,
    ) {
    }

    public function createNotification(?User $sender = null, ?User $receiver = null, string $blocKey = null, array $params = [], bool $private = false):
    Notification
    {
        $notification = new Notification();

        $notification->setSender($sender);
        $notification->setReceiver($receiver);
        $notification->setPrivate($private);

        $student = $receiver?->getStudent();
        if($student instanceof Student){
            $notification->setIdentifier($student->getIdentifier());
        }

        if (null != $blocKey) {
            $bloc = $this->blocRepository->findActiveByKey($blocKey);
            if (null === $bloc) {
                throw new BlocNotFoundException($blocKey);
            }
            $subject = $bloc->getLabel()?? '';
            $content = $bloc->getContent()?? '';

            foreach ($params as $key => $value) {
                if (is_string($value)) {
                    $subject = str_replace("%$key%", $value, $subject);
                    $content = str_replace("%$key%", $value, $content);
                }
            }
        } else {
            $subject = $params['subject'] ?? '';
            $content = $params['content'] ?? '';
        }

        $notification->setSubject($subject);
        $notification->setContent($content);

        return $notification;
    }

    public function getParentThread(Notification $notification): Notification
    {
        while (null !== $notification->getParent()) {
            $notification = $notification->getParent();
        }

        return $notification;
    }
}
