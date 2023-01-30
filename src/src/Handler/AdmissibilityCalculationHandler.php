<?php

namespace App\Handler;

use App\Constants\Notification\NotificationConstants;
use App\Entity\User;
use App\Exception\Admissibility\AdmissibilityNotFoundException;
use App\Exception\Admissibility\CalculatorNotFoundException;
use App\Exception\Bloc\BlocNotFoundException;
use App\Manager\NotificationManager;
use App\Message\AdmissibilityCalculation;
use App\Repository\Admissibility\CalculatorRepository;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Service\Admissibility\AdmissibilityNoteManager;
use App\Service\Notification\NotificationCenter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AdmissibilityCalculationHandler implements MessageHandlerInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
        private AdmissibilityNoteManager $admissibilityNoteManager,
        private LoggerInterface $logger,
        private CalculatorRepository $calculatorRepository,
        private EntityManagerInterface $manager,
        private NotificationCenter $notificationCenter,
        private NotificationManager $notificationManager,
        private UserRepository $userRepository
    ) {}

    public function __invoke(AdmissibilityCalculation $message): void
    {
        /** @var User $user */
        $user = $this->userRepository->find($message->getUserId());

        $calculator = $this->calculatorRepository->find($message->getCalculatorId());
        if (null === $calculator) {
            $notification = $this->notificationManager->createNotification(null, $user, 'RANKING_ADMISSIBILITY_NOTIFICATION_ERROR');
            $this->notificationCenter->dispatch(
                $notification,
                [
                    NotificationConstants::TRANSPORT_EMAIL,
                    NotificationConstants::TRANSPORT_DB
                ],
                sendGenericMail: false
            );

            throw new CalculatorNotFoundException(calculatorId: $message->getCalculatorId());
        }

        $calculator->setLastLaunchDate(new DateTime());
        $calculator->setRunning(true);
        $this->manager->flush();

        try {
            $students = $this->studentRepository->fetchStudentsForHandler();
            foreach ($students as $student) {
                try {
                    $this->admissibilityNoteManager->update($student);
                } catch (AdmissibilityNotFoundException $e) {
                    $this->logger->error($e->getMessage());
                    continue;
                }
            }
            $calculator->setRunning(false);
            $this->manager->flush();

            $notification = $this->notificationManager->createNotification(null, $user, 'RANKING_ADMISSIBILITY_NOTIFICATION');
            $this->notificationCenter->dispatch(
                $notification,
                [
                    NotificationConstants::TRANSPORT_EMAIL,
                    NotificationConstants::TRANSPORT_DB
                ],
                sendGenericMail: false
            );
        } catch (BlocNotFoundException|Exception $e) {
            $calculator->setRunning(false);
            $this->manager->flush();

            $notification = $this->notificationManager->createNotification(null, $user, 'RANKING_ADMISSIBILITY_NOTIFICATION_ERROR');
            $this->notificationCenter->dispatch(
                $notification,
                [
                    NotificationConstants::TRANSPORT_EMAIL,
                    NotificationConstants::TRANSPORT_DB
                ],
                sendGenericMail: false
            );
        }
    }
}