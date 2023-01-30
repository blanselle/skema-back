<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\UserRoleConstants;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\ParameterManager;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * Check if a BO user can sends an notification admissibility result to the target student
 */
class SendNotificationAdmissibilityVoter extends Voter
{
    public const ACTION_SEND_ADMISSIBILITY = 'send-admissibility';

    public function __construct(
        private ParameterManager $parameterManager,
        private Security $security,
        private LoggerInterface $logger,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!in_array($attribute, [self::ACTION_SEND_ADMISSIBILITY], true)) {
            return false;
        }

        if(null !== $subject and !$subject instanceof Student) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var ?Student $subject */

        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($attribute === self::ACTION_SEND_ADMISSIBILITY) {
            return $this->canSend($user, $subject);
        }

        $this->logger->error('SendAdmissibilityVoter: Invalid attribute', [
            'attribute' => $attribute,
            'subject' => $subject,
        ]);

        return false;
    }

    private function canSend(User $user, ?Student $student = null): bool
    {
        if(!$this->security->isGranted(UserRoleConstants::ROLE_RESPONSABLE, $user)) {
            return false;
        }

        if(
            null !== $student and 
            !in_array(
                $student->getState(), 
                [StudentWorkflowStateConstants::STATE_ADMISSIBLE, StudentWorkflowStateConstants::STATE_REJECTED_ADMISSIBLE],
                true,
            )
        ) {
            return false;
        }

        $now = new DateTime();

        if($now < $this->parameterManager->getParameter('dateClotureInscriptions')) {
            return false;
        }

        if($now > $this->parameterManager->getParameter('dateResultatsAdmission')) {
            return false;
        }
        
        return true;
    }
}