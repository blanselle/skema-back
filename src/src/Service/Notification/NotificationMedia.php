<?php

declare(strict_types=1);

namespace App\Service\Notification;

use App\Entity\Media;
use App\Entity\Notification\Notification;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Manager\NotificationManager;
use App\Repository\BlocRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotificationMedia
{
    private const HEADER_BLOC_KEY = 'NOTIFICATION_JUSTIFICATIF_REJECTED';

    public function __construct(
        private BlocRepository $blocRepository,
        private TranslatorInterface $translator,
        private UserRepository $userRepository,
        private Security $security,
        private NotificationManager $notificationManager,
    ) {
    }

    public function generateRejectedNotification(Media $media, string $subject, string $content): Notification
    {
        $bloc = $this->blocRepository->findOneBy(['key' => self::HEADER_BLOC_KEY]);

        if(null === $bloc) {
            throw new BlocNotFoundException(self::HEADER_BLOC_KEY);
        }

        $headerNotificationContent = str_replace(
            '%type%', 
            $this->translator->trans('media.codes.'.strtolower($media->getCode())),
            $bloc->getContent()
        );
        
        $params = [
            'subject' => $subject,
            'content' => sprintf("%s\n%s",
                $headerNotificationContent,
                $content,
            ),    
        ];

        return $this->notificationManager->createNotification(
            receiver: $media->getStudent()->getUser(),
            params: $params,
        );
    }

    public function generateTransferredNotification(string $subject, string $content, string $receiverId, Media $media): Notification
    {
        /** @var User|null $receiver */
        $receiver = $this->userRepository->findOneBy(['id' => $receiverId]);

        if(null === $receiver) {
            throw new Exception('Receiver not found');
        }

        $studentIdentifier = $media->getStudent()?->getIdentifier();
        if(null === $studentIdentifier) {
            throw new Exception('Error transferred media: studentIdentifier does not have identifier');
        }

        /** @var User|null $currentUser */
        $currentUser = $this->security->getUser();

        $params = [
            'subject' => $subject,
            'content' => sprintf(
                'Transfert du document %s pour la candidature %s. %s',
                $this->translator->trans('media.codes.'.strtolower($media->getCode())),
                $studentIdentifier,
                $content,
            ),
        ];

        return $this->notificationManager->createNotification(
            sender: $currentUser,
            receiver: $receiver,
            params: $params,
        );
    }
                
}
