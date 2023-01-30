<?php

declare(strict_types=1);

namespace App\Handler;

use App\Constants\Notification\NotificationConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Notification\Notification;
use App\Message\ApproveAllCandidacy;
use App\Repository\StudentRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotificationCenter;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ApproveAllCandidacyHandler implements MessageHandlerInterface
{
    public function __construct(
        private StudentRepository $studentRepository,
        private StudentWorkflowManager $studentWorkflowManager,
        private NotificationCenter $notificationCenter,
        private UserRepository $userRepository,
    ) {}

    public function __invoke(ApproveAllCandidacy $message): void
    {
        $students = $this->studentRepository->findBy(['state' => StudentWorkflowStateConstants::STATE_COMPLETE]);

        $nbStudentApproved = 0;
        $studentNoApprovedList = '';
        
        foreach ($students as $student) {
            if ($this->studentWorkflowManager->completeToApproved($student)) {
                $nbStudentApproved++;
            } else {
                $studentNoApprovedList .= sprintf(
                    "<li> %s - %s %s</li>",
                    $student->getIdentifier(),
                    $student->getUser()->getLastName(),
                    $student->getUser()->getFirstName(),
                );
            } 
        }

        $nbStudentNoApproved = count($students) - $nbStudentApproved;
        $notification = (new Notification())
            ->setReceiver($this->userRepository->find($message->getUserId()))
            ->setSubject('Approbation des candidats terminé')
            ->setContent(<<<EOF
                L’approbation des candidats est terminée. <br />
            
                <strong>{$nbStudentApproved}</strong> candidatures approuvées. <br />
            
                <strong>{$nbStudentNoApproved}</strong> candidatures non approuvées : <br />
                <ul>
                {$studentNoApprovedList}
                </ul>
            EOF);

        $this->notificationCenter->dispatch(
            $notification, 
            [NotificationConstants::TRANSPORT_DB], 
            sendGenericMail: false,
        );
    }
}