<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Constants\Mail\MailConstants;
use App\Constants\User\UserRoleConstants;
use App\Entity\Bloc\Bloc;
use App\Entity\Notification\Notification as NotificationEntity;
use App\Entity\Student;
use App\Entity\User;
use App\Service\Mail\EmailFromConfig;
use App\Service\Mail\MailerEngine;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class MailNotification extends AbstractNotification
{
    private const SUBJECT = 'subject';
    private const CONTENT = 'content';

    public function __construct(
        private MailerEngine $mailer,
        private EntityManagerInterface $em,
        private EmailFromConfig $emailFromConfig,
    ) {
    }

    private function getReceivers(NotificationEntity $notificationCenter): array
    {
        $recievers = array_merge(
            $this->getRecieversFromRoles($notificationCenter->getRoles()),
            $this->getRecieversFromProgramChannels($notificationCenter->getProgramChannels()),
        );

        if (null !== $notificationCenter->getReceiver() && !in_array($notificationCenter->getReceiver()->getEmail(), $recievers, true)) {
            $recievers[] = $notificationCenter->getReceiver()->getEmail();
        }

        return $recievers;
    }

    private function getRecieversFromRoles(array $roles): array
    {
        $recievers = [];
        foreach ($roles as $role) {
            if (in_array($role, UserRoleConstants::getConsts(), true)) {
                $users = $this->em->getRepository(User::class)->findBy(['roles' => $role]);
                /** @var User $user */
                foreach ($users as $user) {
                    $recievers[] = $user->getEmail();
                }
            }
        }

        return $recievers;
    }

    private function getRecieversFromProgramChannels(Collection $programChannels): array
    {
        $recievers = [];
        foreach ($programChannels as $programChannel) {
            $students = $this->em->getRepository(Student::class)->findBy(['programChannel' => $programChannel]);
            foreach ($students as $student) {
                $recievers[] = $student->getUser()->getEmail();
            }
        }

        return $recievers;
    }

    private function getMessage(NotificationEntity $notificationCenter, bool $sendGenericMail): array
    {
        $student = $notificationCenter->getReceiver()?->getStudent();

        if (null !== $student and null !== $notificationCenter->getIdentifier() and $sendGenericMail) {
            /**
             * @var Bloc $bloc
             */
            $bloc = $this->em->getRepository(Bloc::class)->findOneBy(['key' => 'NOTIFICATION_MESSAGE_STUDENT']);
            if (null != $bloc) {
                return [
                    self::SUBJECT => $bloc->getLabel(),
                    self::CONTENT => $bloc->getContent()
                ];
            }
        }
        return [
            self::SUBJECT => $notificationCenter->getSubject(),
            self::CONTENT => $notificationCenter->getContent()
        ];
    }

    public function send(NotificationEntity $notificationCenter, bool $sendGenericMail = true): void
    {
        $receivers = $this->getReceivers($notificationCenter);
        $message = $this->getMessage($notificationCenter, $sendGenericMail);

        $this->mailer->dispatch($receivers, $message[self::SUBJECT], $message[self::CONTENT], $this->emailFromConfig->get(MailConstants::MAIL_SC));
    }
}
