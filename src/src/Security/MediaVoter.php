<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\UserRoleConstants;
use App\Entity\Media;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class MediaVoter extends Voter
{
    public const ACTION_ACCEPT    = 'accept';
    public const ACTION_REJECT    = 'reject';
    public const ACTION_TRANSFERT = 'transfert';
    public const ACTION_REACCEPT = 'reaccept';

    public function __construct(private LoggerInterface $logger, private Security $security){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ACTION_ACCEPT, self::ACTION_REJECT, self::ACTION_TRANSFERT, self::ACTION_REACCEPT], true)) {
            return false;
        }

        if (!$subject instanceof Media) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Media $subject */

        /** @phpstan-ignore-next-line */
        return match($attribute) {
            self::ACTION_ACCEPT => $this->canApprouve($subject, $user),
            self::ACTION_REJECT => $this->canReject($subject, $user),
            self::ACTION_TRANSFERT => $this->canTransfert($subject, $user),
            self::ACTION_REACCEPT => $this->canReApprouve($subject, $user),
            default => function() use ($attribute, $subject): bool {

                $this->logger->error('MediaVoter: Invalid attribute', [
                    'attribute' => $attribute,
                    'media' => $subject,
                ]);

                return false;
            }
        };
    }

    public function canApprouve(Media $media, User $user): bool
    {
        if($media->getState() === MediaWorflowStateConstants::STATE_ACCEPTED) {
            return false;
        }

        if(!$this->security->isGranted(UserRoleConstants::ROLE_COORDINATOR, $user)) {
            return false;
        }

        return true;
    }

    public function canReApprouve(Media $media, User $user): bool
    {
        if($media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED) {
            return false;
        }

        if(!$this->security->isGranted(UserRoleConstants::ROLE_COORDINATOR, $user)) {
            return false;
        }

        return true;
    }

    public function canReject(Media $media, User $user): bool
    {
        if($media->getState() === MediaWorflowStateConstants::STATE_REJECTED) {
            return false;
        }
        if($media->getState() === MediaWorflowStateConstants::STATE_ACCEPTED) {
            if(!$this->security->isGranted(UserRoleConstants::ROLE_RESPONSABLE, $user)) {
                return false;
            }
    
            $studentAuthorizedStates = [
                StudentWorkflowStateConstants::STATE_CHECK_DIPLOMA,
                StudentWorkflowStateConstants::STATE_CREATED,
                StudentWorkflowStateConstants::STATE_CHECK_BOURSIER,
                StudentWorkflowStateConstants::STATE_RECHECK_BOURSIER,
                StudentWorkflowStateConstants::STATE_CREATED_TO_PAY,
                StudentWorkflowStateConstants::STATE_VALID,
                StudentWorkflowStateConstants::STATE_ELIGIBLE,
                StudentWorkflowStateConstants::STATE_COMPLETE,  
                StudentWorkflowStateConstants::STATE_COMPLETE_PROOF,
                StudentWorkflowStateConstants::STATE_BOURSIER_KO,
            ];
    
            if(!in_array($media->getStudent()?->getState(), $studentAuthorizedStates, true)) {
                return false;
            }
    
            return true;
        }

        if(!$this->security->isGranted(UserRoleConstants::ROLE_COORDINATOR, $user)) {
            return false;
        }

        return true;
    }

    public function canTransfert(Media $media, User $user): bool
    {
        if($media->getState() !== MediaWorflowStateConstants::STATE_TO_CHECK) {
            return false;
        }

        if(!$this->security->isGranted(UserRoleConstants::ROLE_COORDINATOR, $user)) {
            return false;
        }

        return true;
    }
}