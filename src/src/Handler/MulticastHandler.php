<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\User;
use App\Message\MulticastMessage;
use App\Repository\UserRepository;
use App\Repository\StudentRepository;
use App\Entity\Notification\Notification;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Constants\Notification\NotificationConstants;
use App\Service\Notification\NotificationTransformer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MulticastHandler implements MessageHandlerInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
        private UserRepository $userRepository,
        private NotificationTransformer $notificationTransformer,
        private MessageBusInterface $bus,
    ) {
    }

    public function __invoke(MulticastMessage $multicastMessage): void
    {
        $students = $this->studentRepository->findBy(['id' => $multicastMessage->getStudentIds()]);

        $sender = null;
        if(null !== $multicastMessage->getSenderId())  {
            /** @var User|null $sender */
            $sender = $this->userRepository->findOneBy(['id' => $multicastMessage->getSenderId()]);
        }

        $roleSender = [];
        if(!empty($multicastMessage->getRoleSender()))  {
            $roleSender = $multicastMessage->getRoleSender();
        }

        foreach($students as $student) {

            $notificationMessage = $this->notificationTransformer->transform(
                (new Notification())
                    ->setReceiver($student->getUser())
                    ->setSubject($multicastMessage->getSubject())
                    ->setContent($multicastMessage->getContent())
                    ->setSender($sender)
                    ->setRoles($student->getUser()?->getRoles() ?? [])
                    ->setRoleSender($roleSender)
                , 
                [NotificationConstants::TRANSPORT_DB],
                sendGenericMail: true,
            );

            $this->bus->dispatch($notificationMessage);
        }
    }
}
