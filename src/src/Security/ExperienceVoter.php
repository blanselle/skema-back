<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\CV\Experience;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\User;
use App\Security\Traits\EndSubscriptionVoterTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * https://pictime.atlassian.net/browse/SB-167
 */
class ExperienceVoter extends Voter
{
    use EndSubscriptionVoterTrait;

    public const ACTION_EDIT = 'edit';
    public const ACTION_CREATE = 'create';
    public const ACTION_DELETE = 'delete';

    public function __construct(
        private LoggerInterface $logger,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!in_array($attribute, [self::ACTION_EDIT, self::ACTION_CREATE, self::ACTION_DELETE], true)) {
            return false;
        }
        
        if($subject instanceof Experience && in_array($attribute, [self::ACTION_CREATE, self::ACTION_DELETE], true)) {
            return true;
        }

        if(!is_array($subject)) {
            return false;
        }

        if(count($subject) !== 2) {
            return false;
        }

        if(!isset($subject['original'])) {
            return false;
        }

        if(!isset($subject['object'])) {
            return false;
        }

        if(!$subject['original'] instanceof Experience) {
            return false;
        };

        if(!$subject['object'] instanceof Experience) {
            return false;
        };

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if($user->getStudent() === null) {
            return false;
        }

        if($attribute === self::ACTION_DELETE) {
            return $this->canDelete($user->getStudent());
        }

        if($attribute === self::ACTION_CREATE) {
            return $this->canCreate($user->getStudent());
        }

        if($attribute === self::ACTION_EDIT) {
            return $this->canEdit($subject['original'], $subject['object'], $user->getStudent());
        }

        $this->logger->error('ExperienceVoter: Invalid attribute', [
            'attribute' => $attribute,
            'subject' => $subject,
        ]);

        return false;
    }

    private function canCreate(Student $student): bool
    {
        if((new DateTime()) < $this->getDate($student)) {
            return true;
        }

        throw new AccessDeniedException($this->getMessageError($student));
    }

    private function canDelete(Student $student): bool
    {
        if((new DateTime()) <= $this->getDateFinCV($student)) {
            return true;
        }

        throw new AccessDeniedException($this->getMessageError($student));
    }

    private function canEdit(Experience $originalExperience, Experience $updatedExperience, Student $student): bool
    {
        if((new DateTime()) <= $this->getDateFinCV($student)) {
            return true;
        }

        /** @var PropertyAccessor $propertyAccessor */
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $class = new ReflectionClass(Experience::class);

        foreach($class->getProperties() as $property) {
            $field = $property->getName();
            
            $originalValue = $propertyAccessor->getValue($originalExperience, $field);
            $newValue = $propertyAccessor->getValue($updatedExperience, $field);

            // WORKAROUND: collections are ignored because we can't get the old value of collections
            if($originalValue instanceof Collection) {
                continue;
            }
            
            if($originalValue === $newValue) {
                continue;
            }
            
            if($originalValue instanceof Media && in_array($originalValue->getState(), [MediaWorflowStateConstants::STATE_CANCELLED, MediaWorflowStateConstants::STATE_REJECTED], true)) {
                continue;
            }
            
            throw new AccessDeniedException($this->getMessageError($student));
        }

        return true;
    }
}