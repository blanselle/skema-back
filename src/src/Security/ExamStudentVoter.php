<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\Exam\ExamSessionTypeCodeConstants;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Exam\ExamStudent;
use App\Entity\User;
use App\Manager\ParameterManager;
use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * https://pictime.atlassian.net/browse/SB-194
 */
class ExamStudentVoter extends Voter
{
    public const ACTION_CREATE          = 'create';
    public const ACTION_EDIT            = 'edit';

    public function __construct(
        private ParameterManager $parameterManager,
        private LoggerInterface $logger,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {        
        return $subject instanceof ExamStudent && in_array($attribute, [self::ACTION_CREATE, self::ACTION_EDIT], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($attribute === self::ACTION_CREATE) {
            return $this->canCreate($subject, $user);
        }

        if($attribute === self::ACTION_EDIT) {
            return $this->canEdit($subject, $user);
        }

        $this->logger->error('ExamStudentVoter: Invalid attribute', [
            'attribute' => $attribute,
            'subject' => $subject,
        ]);

        return false;
    }

    public function canCreate(ExamStudent $examStudent, User $user): bool
    {
        if((new DateTime()) <= $this->getDateClotureInscription($user)) {
            return true;
        }
        
        if($examStudent->getExamSession()->getType() === ExamSessionTypeConstants::TYPE_OUTSIDE) {
            return true;
        }

        return false;
    }

    public function canEdit(ExamStudent $examStudent, User $user): bool
    {
        if((new DateTime()) <= $this->getDateClotureInscription($user)) {
            return true;
        }
        
        if($examStudent->getExamSession()->getType() === ExamSessionTypeConstants::TYPE_OUTSIDE) {
            return true;
        }

        if($examStudent->getExamSession()->getExamClassification()->getExamSessionType()->getCode() === ExamSessionTypeCodeConstants::MANAGEMENT && $examStudent->getExamSession()->getExamClassification()->getKey() !== 'gmat'){
            return false;
        }

        if($examStudent->getMedia() === null || $examStudent->getMedia()->getState() === MediaWorflowStateConstants::STATE_REJECTED) {
            return true;
        }

        return false;
    }

    private function getDateClotureInscription(User $user): DateTime
    {
        return $this->parameterManager->getParameter('dateClotureInscriptions', $user->getStudent()->getProgramChannel())->getValue();
    }
}