<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\ParameterManager;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * https://pictime.atlassian.net/browse/SB-136
 */
class AdministrativeRecordVoter extends Voter
{
    public const ACTION_EDIT = 'edit';

    public function __construct(
        private ParameterManager $parameterManager,
        private LoggerInterface $logger,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(!in_array($attribute, [self::ACTION_EDIT], true)) {
            return false;
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

        if(!$subject['original'] instanceof AdministrativeRecord) {
            return false;
        };

        if(!$subject['object'] instanceof AdministrativeRecord) {
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

        if($attribute === self::ACTION_EDIT) {
            return $this->canEdit($subject['original'], $subject['object'], $user->getStudent());
        }

        $this->logger->error('AdministrativeRecordVoter: Invalid attribute', [
            'attribute' => $attribute,
            'subject' => $subject,
        ]);

        return false;
    }

    private function canEdit(AdministrativeRecord $originalAdministrativeRecord, AdministrativeRecord $updatedAdministrativeRecord, Student $student): bool
    {
        if((new DateTime()) <= $this->getDateClotureInscription($student)) {
            return true;
        }

        /** @var PropertyAccessor $propertyAccessor */
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $class = new ReflectionClass(AdministrativeRecord::class);

        foreach($class->getProperties() as $property) {
            $field = $property->getName();
            
            $originalValue = $propertyAccessor->getValue($originalAdministrativeRecord, $field);
            $newValue = $propertyAccessor->getValue($updatedAdministrativeRecord, $field);

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
            
            return false;
        }

        return true;
    }

    private function getDateClotureInscription(Student $student): DateTime
    {
        return $this->parameterManager->getParameter('dateClotureInscriptions', $student->getProgramChannel())->getValue();
    }
}