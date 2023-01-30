<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\UserRoleConstants;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\User\StudentWorkflowHistoryRepository;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class OralTestVoter extends Voter
{
    public const ACTION_EDIT = 'edit-oral-test';

    public function __construct(
        private StudentWorkflowHistoryRepository $studentWorkflowHistoryRepository,
        private Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Student and in_array($attribute, [self::ACTION_EDIT], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var Student $subject */

        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::ACTION_EDIT => $this->canEdit($subject, $user),
            default => throw new LogicException('This code should not be reached!')
        };
    }

    public function canEdit(Student $student, User $user): bool
    {
        if($this->studentWorkflowHistoryRepository->findOneBy([
            'state' => StudentWorkflowStateConstants::STATE_ADMISSIBLE,
            'student' => $student,
        ]) === null) {
            return false;
        }

        if(!$this->security->isGranted(UserRoleConstants::ROLE_COORDINATOR, $user)) {
            return false;
        }

        return true;
    }
}