<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Student;

use App\Constants\Errors\ErrorsConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Service\CandidateManager;
use App\Service\Utils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

class OnAuthenticationSuccessEvent implements EventSubscriberInterface
{
    public function __construct(
        private CandidateManager $candidateManager,
        private Utils $utils,
        private StudentRepository $studentRepository
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationSuccessEvent::class => 'onAuthenticationSuccessEvent'
        ];
    }

    public function onAuthenticationSuccessEvent(AuthenticationSuccessEvent $event): void
    {
        $student = $event->getAuthenticationToken()->getUser()?->getStudent();

        if (!$student instanceof Student || null == $student) {
            return;
        }

        $state = $this->studentRepository->getStudentState(student: $student);

        if (in_array($state, [
            StudentWorkflowStateConstants::STATE_REJECTED,
            StudentWorkflowStateConstants::STATE_REJECTED_DIPLOMA,
            StudentWorkflowStateConstants::STATE_REJECTED_ELIGIBLE,
        ], true)) {
            throw new AccessDeniedHttpException($this->utils->getMessageByKey(ErrorsConstants::AUTH_REFUSED));
        }

        if (in_array($state, [
            StudentWorkflowStateConstants::STATE_RESIGNATION_PAYMENT,
            StudentWorkflowStateConstants::STATE_RESIGNATION,
            StudentWorkflowStateConstants::STATE_CANCELED,
            StudentWorkflowStateConstants::STATE_CANCELED_PAYMENT,
        ], true)) {
            throw new AccessDeniedHttpException(
                $this->utils->getMessageByKey(ErrorsConstants::ERROR_CONNEXION_RESIGNATION)
            );
        }

        if (in_array($state, [
            StudentWorkflowStateConstants::STATE_EXEMPTION,
        ], true)) {
            throw new AccessDeniedHttpException(
                $this->utils->getMessageByKey(ErrorsConstants::ERROR_CANDIDATE_EXEMPTION)
            );
        }

        if ($state === StudentWorkflowStateConstants::STATE_START) {
            throw new AccessDeniedHttpException(
                $this->utils->getMessageByKey(ErrorsConstants::UNACTIVE_ACCOUNT)
            );
        }

        $programChannel = $this->studentRepository->getStudentProgramChannel(student: $student);

        // After the subscription periode AND The payment has not been made
        if (
            $this->candidateManager->isDateInscriptionTooLate(programChannel: $programChannel)
            && StudentWorkflowStateConstants::STATE_DECLINED_PAYMENT === $student->getState()
        ) {
            throw new AccessDeniedHttpException(
                $this->utils->getMessageByKey(ErrorsConstants::AUTH_REFUSED_PAYEMENT)
            );
        }

        if ($this->candidateManager->isDateInscriptionTooEarly(programChannel: $programChannel)) {
            throw new AccessDeniedHttpException(
                $this->utils->getMessageByKey(ErrorsConstants::ERROR_CANDIDATE_INSCRIPTION_NOT_OPEN)
            );
        }
    }
}
