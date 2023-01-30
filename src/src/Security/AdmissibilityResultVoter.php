<?php

namespace App\Security;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\Student;
use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AdmissibilityResultVoter extends Voter
{
    public const ACTION_SHOW_RESULT = 'show_result';


    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Student and in_array($attribute, [self::ACTION_SHOW_RESULT], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::ACTION_SHOW_RESULT => $this->canShowResult($subject),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    private function canShowResult(mixed $subject): bool
    {
        return in_array($subject->getState(), [StudentWorkflowStateConstants::STATE_ADMISSIBLE, StudentWorkflowStateConstants::STATE_REJECTED_ADMISSIBLE], true);
    }
}