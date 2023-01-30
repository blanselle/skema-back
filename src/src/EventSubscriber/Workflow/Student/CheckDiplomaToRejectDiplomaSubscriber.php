<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Student;

use App\Constants\Mail\MailConstants;
use App\Constants\User\StudentWorkflowTransitionConstants;
use App\Entity\Student;
use App\Exception\Bloc\BlocNotFoundException;
use App\Repository\BlocRepository;
use App\Service\Mail\EmailFromConfig;
use App\Service\Mail\MailerEngine;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class CheckDiplomaToRejectDiplomaSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private BlocRepository $blocRepository,
        private MailerEngine $mailer,
        private EmailFromConfig $emailFromConfig,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.candidate.transition.%s',
                StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_REJECTED_DIPLOMA
            ) => 'checkDiplomaToRejectDiploma',
        ];
    }

    public function checkDiplomaToRejectDiploma(TransitionEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        $bloc = $this->blocRepository->findActiveByKey('NOTIFICATION_CHECK_DIPLOMA_TO_REJECTED_DIPLOMA');

        if (null === $bloc) {
            throw new BlocNotFoundException('NOTIFICATION_CHECK_DIPLOMA_TO_REJECTED_DIPLOMA');
        }

        $subject = $bloc->getLabel();
        $content = $bloc->getContent();

        if (!empty($content)) {
            $content = str_replace("%firstname%", $student->getUser()->getFirstName(), $content);
        }
        
        $this->mailer->dispatch(
            to: [$student->getUser()->getEmail()],
            subject: $subject,
            body: $content,
            from: $this->emailFromConfig->get(MailConstants::MAIL_SC),
        );
    }
}
