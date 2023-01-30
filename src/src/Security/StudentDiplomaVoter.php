<?php

declare(strict_types=1);

namespace App\Security;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Diploma\StudentDiploma;
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
class StudentDiplomaVoter extends Voter
{
    public const ACTION_CREATE = 'create';
    public const ACTION_EDIT = 'edit';

    public function __construct(
        private ParameterManager $parameterManager,
        private LoggerInterface $logger,
    ){}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if(in_array($attribute, [self::ACTION_CREATE], true) && $subject instanceof StudentDiploma) {
            return true;
        }
            
        if(in_array($attribute, [self::ACTION_EDIT], true)) {
            
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
            
            if(!$subject['original'] instanceof StudentDiploma) {
                return false;
            };
            
            if(!$subject['object'] instanceof StudentDiploma) {
                return false;
            };

            return true;
        }

        return false;
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

        /** @phpstan-ignore-next-line */
        return match($attribute) {
            self::ACTION_EDIT => $this->canEdit($subject['original'], $subject['object'], $user->getStudent()),
            self::ACTION_CREATE => $this->canCreate($user->getStudent()),
            default => function() use ($attribute, $subject): bool {

                $this->logger->error('StudentDiplomaVoter: Invalid attribute', [
                    'attribute' => $attribute,
                    'media' => $subject,
                ]);

                return false;
            }
        };
    }

    private function canEdit(StudentDiploma $originalStudentDiploma, StudentDiploma $updatedStudentDiploma, Student $student): bool
    {
        if((new DateTime()) <= $this->getDateClotureInscription($student)) {
            return true;
        }

        /** @var PropertyAccessor $propertyAccessor */
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $class = new ReflectionClass(StudentDiploma::class);

        foreach($class->getProperties() as $property) {

            $field = $property->getName();
            
            $originalValue = $propertyAccessor->getValue($originalStudentDiploma, $field);
            $newValue = $propertyAccessor->getValue($updatedStudentDiploma, $field);
            
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

    private function canCreate(Student $student): bool
    {
        return (new DateTime()) < $this->getDateClotureInscription($student);
    }

    private function getDateClotureInscription(Student $student): DateTime
    {
        return $this->parameterManager->getParameter('dateClotureInscriptions', $student->getProgramChannel())->getValue();
    }
}