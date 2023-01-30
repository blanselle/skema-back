<?php

namespace App\Handler;

use App\Constants\Notification\NotificationConstants;
use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Manager\NotificationManager;
use App\Message\EligibleStudents;
use App\Repository\Admissibility\CalculatorRepository;
use App\Repository\ProgramChannelRepository;
use App\Repository\UserRepository;
use App\Service\Admissibility\AdmissibilityManager;
use App\Service\Notification\NotificationCenter;
use App\Service\Workflow\Student\StudentWorkflowManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EligibleStudentsHandler implements MessageHandlerInterface
{
    public function __construct(
        private ProgramChannelRepository $programChannelRepository,
        private AdmissibilityManager $admissibilityManager,
        private StudentWorkflowManager $workflowManager,
        private CalculatorRepository $calculatorRepository,
        private EntityManagerInterface $manager,
        private NotificationCenter $notificationCenter,
        private NotificationManager $notificationManager,
        private UserRepository $userRepository,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(EligibleStudents $message): void
    {
        $calculator = $this->calculatorRepository->find($message->getCalculatorId());
        $calculator->setLastLaunchDate(new DateTime());
        $calculator->setRunning(true);

        /** @var User $user */
        $user = $this->userRepository->find($message->getUserId());

        try {
            $this->manager->flush();

            $score = $message->getScore();
            $programChannels = [];
            foreach ($message->getProgramChannelIds() as $id) {
                $programChannels[] = $this->programChannelRepository->find((int)$id);
            }

            try {
                $eligibleStudents = $this->admissibilityManager->getEligibleStudents($programChannels, $score);
                $notEligibleStudents = $this->admissibilityManager->getEligibleStudents($programChannels, $score, false);

                foreach ($eligibleStudents as $value) {
                    foreach ($value as $student) {
                        $this->workflowManager->admissible($student);
                    }
                }

                foreach ($notEligibleStudents as $value) {
                    foreach ($value as $student) {
                        $this->workflowManager->rejectedAdmissible($student);
                    }
                }
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
                return;
            }

            $calculator->setRunning(false);
            $this->manager->flush();

            $notification = $this->notificationManager->createNotification(null, $user, 'RANKING_SIMULATOR_NOTIFICATION');

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