<?php

declare(strict_types=1);

namespace App\Handler;

use App\Constants\Notification\NotificationConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Notification\Notification;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Message\NotificationsAdmissibilityResultMessage;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationAdmissibilityResultDispatcher;
use App\Service\Notification\NotificationCenter;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

#[AsMessageHandler]
class NotificationsAdmissibilityResultHandler implements MessageHandlerInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
        private LoggerInterface $logger,
        private NotificationAdmissibilityResultDispatcher $dispatcher,
        private NotificationCenter $notificationCenter,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(NotificationsAdmissibilityResultMessage $message): void
    {
        $students = $this->studentRepository->findBy(['state' => [StudentWorkflowStateConstants::STATE_ADMISSIBLE, StudentWorkflowStateConstants::STATE_REJECTED_ADMISSIBLE]]);

        foreach($students as $student) {
            try {
                $this->dispatcher->dispatch($student);
            } catch(BlocNotFoundException $e) {
                $this->logger->error(sprintf('NotificationsAdmissibilityResult %s', $e->getMessage()));
            }
        }
        
        /** @var User|null $receiver */
        $receiver = $this->userRepository->findOneBy(['email' => $message->getReceiverIdentifier()]);

        if(null === $receiver) {
            throw new UserNotFoundException(sprintf('NotificationsAdmissibilityResultHandler : user %s not found', $message->getReceiverIdentifier()));
        }

        $this->notificationCenter->dispatch(
            (new Notification())
                ->setSubject('Mails résultats admissibilités')
                ->setContent(sprintf('Les résultats d\'admissions ont été envoyés le %s', (new DateTime())->format('d MMMM y à HH:mm')))
                ->setReceiver($receiver)
            ,
            codes: [NotificationConstants::TRANSPORT_DB],
            sendGenericMail: false,
        );
    }
}
