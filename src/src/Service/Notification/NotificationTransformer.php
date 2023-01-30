<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Entity\Notification\Notification;
use App\Entity\Notification\NotificationTemplate;
use App\Entity\ProgramChannel;
use App\Entity\User;
use App\Message\NotificationMessage;
use App\Repository\Notification\NotificationRepository;
use App\Repository\Notification\NotificationTemplateRepository;
use App\Repository\ProgramChannelRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Translate Notification <--> NotificationMessage
 */
class NotificationTransformer
{
    /**
     * Since the dispatch notification on ProgramChannelSwitchManager class
     * if we used all EntityRepository as a service we have this error on cache clear:
     * This repository can be attached only to ORM sortable listener in . (which is being imported from "/
     * srv/skema/config/routes/api_platform.yaml"). Make sure there is a loader supporting the "api_platform" type.
     * We need to use EntityManagerInterface instead of ServiceRepository due to SortableRepository on ProgramChannelRepository
     * @param EntityManagerInterface $manager
     */
    public function __construct(private EntityManagerInterface $manager) {}

    /**
     * Translate Notification to NotificationMessgae
     *
     * @param Notification $notification
     * @return NotificationMessage
     */
    public function transform(Notification $notification, array $codes, bool $sendGenericMail): NotificationMessage
    {
        return (new NotificationMessage())
            ->setParentId($notification->getParent()?->getId())
            ->setSenderId($notification->getSender()?->getId())
            ->setReceiverId($notification->getReceiver()?->getId())
            ->setRoles($notification->getRoles())
            ->setRoleSender($notification->getRoleSender())
            ->setSubject($notification->getSubject())
            ->setContent($notification->getContent())
            ->setIdentifier($notification->getIdentifier())
            ->setComment($notification->getComment())
            ->setProgramChannelsIds(
                array_map(function (ProgramChannel $programChannel) {
                    return $programChannel->getId();
                }, $notification->getProgramChannels()->toArray())
            )
            ->setNotificationTemplateId($notification->getNotificationTemplate()?->getId())
            ->setCodes($codes)
            ->setPrivate($notification->isPrivate())
            ->setSendGenericMail($sendGenericMail)
        ;
    }

    /**
     * Transform notificationMessage to notification
     *
     * @param NotificationMessage $notificationMessage
     * @return array Notification + code
     */
    public function reverse(NotificationMessage $notificationMessage): array
    {
        $notification =(new Notification())
            ->setParent($this->getParent($notificationMessage))
            ->setSender($this->getUser($notificationMessage->getSenderId()))
            ->setReceiver($this->getUser($notificationMessage->getReceiverId()))
            ->setRoles($notificationMessage->getRoles())
            ->setRoleSender($notificationMessage->getRoleSender())
            ->setSubject($notificationMessage->getSubject())
            ->setContent($notificationMessage->getContent())
            ->setIdentifier($notificationMessage->getIdentifier())
            ->setComment($notificationMessage->getComment())
            ->setProgramChannels(new ArrayCollection(
                array_map(function (int $programChannelId) {
                    return $this->getProgramChannel($programChannelId);
                }, $notificationMessage->getProgramChannelsIds())
            ))
            ->setNotificationTemplate($this->getNotificationTemplate($notificationMessage))
            ->setPrivate($notificationMessage->isPrivate())
        ;

        return [$notification, $notificationMessage->getCodes(), $notificationMessage->getSendGenericMail()];
    }

    private function getParent(NotificationMessage $notificationMessage): ?Notification
    {
        /** @var NotificationRepository $notificationRepository */
        $notificationRepository = $this->manager->getRepository(Notification::class);
        if (null !== $notificationMessage->getParentId()) {
            return $notificationRepository->findOneById($notificationMessage->getParentId());
        }

        return null;
    }

    private function getUser(?Uuid $userId): ?User
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->manager->getRepository(User::class);
        if (null !== $userId) {
            return $userRepository->findOneById($userId);
        }

        return null;
    }

    private function getProgramChannel(int $programChannelId): ?ProgramChannel
    {
        /** @var ProgramChannelRepository $repository */
        $repository = $this->manager->getRepository(ProgramChannel::class);
        return $repository->findOneById($programChannelId);
    }

    private function getNotificationTemplate(NotificationMessage $notificationMessage): ?NotificationTemplate
    {
        /** @var NotificationTemplateRepository $notificationTemplateRepository */
        $notificationTemplateRepository = $this->manager->getRepository(NotificationTemplate::class);
        if (null !== $notificationMessage->getNotificationTemplateId()) {
            return $notificationTemplateRepository->findOneById($notificationMessage->getNotificationTemplateId());
        }

        return null;
    }
}
