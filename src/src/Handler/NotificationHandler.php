<?php

declare(strict_types=1);

namespace App\Handler;

use App\Constants\Notification\NotificationConstants;
use App\Entity\Notification\Notification;
use App\Exception\Bloc\BlocNotFoundException;
use App\Message\NotificationMessage;
use App\Repository\BlocRepository;
use App\Service\Notification\NotificationTransformer;
use App\Service\Notification\Notifier;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{
    public function __construct(
        private Notifier $notifier,
        private NotificationTransformer $notificationTransformer,
        private BlocRepository $blocRepository,
    ) {
    }

    public function __invoke(NotificationMessage $notificationMessage): void
    {
        [$notification, $codes, $sendGenericMail] = $this->notificationTransformer->reverse($notificationMessage);

        if(true === $sendGenericMail) {
            
            $bloc = $this->blocRepository->findActiveByKey('NOTIFICATION_MESSAGE_STUDENT');

            if (null === $bloc) {
                throw new BlocNotFoundException('NOTIFICATION_MESSAGE_STUDENT');
            }

            $genericNotification = (new Notification())
                ->setReceiver($notification->getReceiver())
                ->setSubject($bloc->getLabel())
                ->setContent($bloc->getContent())
            ;

            $this->notifier->send($genericNotification, [NotificationConstants::TRANSPORT_EMAIL]);
        }

        $this->notifier->send($notification, $codes, $notificationMessage->getSendGenericMail());
    }
}
