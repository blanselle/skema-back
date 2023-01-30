<?php

namespace App\Loggable;

use App\Constants\Loggable\FieldsConstants;
use App\Constants\Loggable\TypeConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\Experience;
use App\Entity\CV\SchoolReport;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Exam\ExamStudent;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Entity\Notification\Notification;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\ObjectManager;
use Gedmo\Loggable\Entity\LogEntry;
use Gedmo\Loggable\LoggableListener as GedmoLoggableListener;
use Gedmo\Loggable\Mapping\Event\LoggableAdapter;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use App\Entity\Admissibility\LandingPage\AdmissibilityStudentToken;

class LoggableListener extends GedmoLoggableListener
{
    private static function attachManyToManyLogEntry(object $logEntry, string $fieldName, Collection $collection): void
    {
        if($collection instanceof PersistentCollection) {
            $insertDiff = $collection->getInsertDiff();
            $deleteDiff = $collection->getDeleteDiff();
        } else {
            $insertDiff = $collection->toArray();
            $deleteDiff = array();
        }

        if (count($insertDiff) > 0 || count($deleteDiff) > 0) {
            /** @phpstan-ignore-next-line */
            $logEntryData = $logEntry->getData();
            $logEntryData[$fieldName] =  array(
                'add' => array_map(function($obj) {
                    return $obj->getId();
                },$insertDiff),
                'remove' => array_map(function($obj) {
                    return $obj->getId();
                },$deleteDiff),
            );

            /** @phpstan-ignore-next-line */
            $logEntry->setData($logEntryData);
        }
    }

    /**
     * Handle any custom LogEntry functionality that needs to be performed
     * before persisting it
     *
     * @param object $logEntry The LogEntry being persisted
     * @param object $object   The object being Logged
     *
     * @return void
     */
    protected function prePersistLogEntry($logEntry, $object)
    {
        if ($object instanceof Cv) {
            self::attachManyToManyLogEntry($logEntry, 'languages', $object->getLanguages());
        }

        if ($object instanceof Bac) {
            self::attachManyToManyLogEntry($logEntry, 'bacTypes', $object->getBacTypes());
        }
    }

    /**
     * Create a new Log instance
     *
     * @param string $action
     * @param object $object
     *
     * @return \Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry|null
     */
    protected function createLogEntry($action, $object, LoggableAdapter $ea)
    {
        $om = $ea->getObjectManager();
        $wrapped = AbstractWrapper::wrap($object, $om);
        $meta = $wrapped->getMetadata();

        // Filter embedded documents
        if (isset($meta->isEmbeddedDocument) && $meta->isEmbeddedDocument) {
            return null;
        }

        $config = $this->getConfiguration($om, $meta->getName());
        if (!empty($config)) {
            $logEntryClass = $this->getLogEntryClass($ea, $meta->getName());
            $logEntryMeta = $om->getClassMetadata($logEntryClass);
            /** @var History|LogEntry $logEntry */
            /** @phpstan-ignore-next-line */
            $logEntry = $logEntryMeta->newInstance();

            $logEntry->setAction($action);
            $logEntry->setUsername($this->username);
            $logEntry->setObjectClass($meta->getName());
            $logEntry->setLoggedAt();

            // add stub attributes for History
            if (method_exists($logEntry, 'setType')) {
                $logEntry->setType($this->getType($om, $meta->getName(), $wrapped->getIdentifier()));
            }
            if (method_exists($logEntry, 'setStudent')) {
                $logEntry->setStudent($this->getStudent($om, $meta->getName(), $wrapped->getIdentifier()));
            }

            // check for the availability of the primary key
            /** @phpstan-ignore-next-line */
            $uow = $om->getUnitOfWork();
            if (self::ACTION_CREATE === $action && $ea->isPostInsertGenerator($meta)) {
                $this->pendingLogEntryInserts[spl_object_id($object)] = $logEntry;
            } else {
                $logEntry->setObjectId($wrapped->getIdentifier());
            }

            if (self::ACTION_REMOVE !== $action && isset($config['versioned'])) {
                $newValues = $this->getObjectChangeSetData($ea, $object, $logEntry);
                foreach ($newValues as $key => $value) {
                    if (FieldsConstants::HISTORY_NOTIFICATION_SENDER === $key || FieldsConstants::HISTORY_NOTIFICATION_RECEIVER === $key) {
                        $newValues[$key] = (string)$value;
                    }
                    if (FieldsConstants::HISTORY_PASSWORD === $key) {
                        $newValues[$key] = FieldsConstants::HISTORY_PASSWORD_PLACEHOLDER;
                    }
                }
                $logEntry->setData($newValues);
            }

            $version = 1;
            if (self::ACTION_CREATE !== $action) {
                $version = $ea->getNewVersion($logEntryMeta, $object);
                if (empty($version)) {
                    // was versioned later
                    $version = 1;
                }
            }
            $logEntry->setVersion($version);

            $this->prePersistLogEntry($logEntry, $object);

            $om->persist($logEntry);
            $uow->computeChangeSet($logEntryMeta, $logEntry);

            return $logEntry;
        }

        return null;
    }

    /**
     * @param ObjectManager $om
     * @param class-string<object> $objectClass
     * @param mixed $objectId
     * @return string
     */
    private function getType(ObjectManager $om, mixed $objectClass, mixed $objectId): string
    {
        $type = match($objectClass) {
            User::class, Student::class => TypeConstants::HISTORY_TYPE_ACCOUNT,
            AdministrativeRecord::class, StudentDiploma::class => TypeConstants::HISTORY_TYPE_ADMINISTRATIVE_RECORD,
            Cv::class, Bac::class, BacSup::class, Experience::class, SchoolReport::class => TypeConstants::HISTORY_TYPE_CV,
            Media::class => TypeConstants::HISTORY_TYPE_DOCUMENT,
            Order::class, Payment::class => TypeConstants::HISTORY_PAYMENT,
            Notification::class => TypeConstants::HISTORY_NOTIFICATION,
            ExamStudent::class => TypeConstants::HISTORY_EXAM_STUDENT,
            AdmissibilityStudentToken::class => TypeConstants::HISTORY_ADMISSIBILITY_TOKEN,
            default => TypeConstants::HISTORY_TYPE_OTHER
        };
        
        if ($objectClass === Media::class) {
            $repo = $om->getRepository($objectClass);
            $media = $repo->findOneBy(['id' => $objectId]);
            $type = match($media?->getCode()) {
                MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE, MediaCodeConstants::CODE_CERTIFICAT_DOUBLE_PARCOURS,
                MediaCodeConstants::CODE_SHN, MediaCodeConstants::CODE_TT, MediaCodeConstants::CODE_CROUS,
                MediaCodeConstants::CODE_ID_CARD, MediaCodeConstants::CODE_JOURNEE_DEFENSE_CITOYENNE
                    => TypeConstants::HISTORY_TYPE_ADMINISTRATIVE_RECORD,
                MediaCodeConstants::CODE_BAC, MediaCodeConstants::CODE_BULLETIN_L1_S1, MediaCodeConstants::CODE_BULLETIN_L1_S2,
                MediaCodeConstants::CODE_BULLETIN_L2_S3, MediaCodeConstants::CODE_BULLETIN_L2_S4, MediaCodeConstants::CODE_BULLETIN_L3_S5,
                MediaCodeConstants::CODE_BULLETIN_L3_S6, MediaCodeConstants::CODE_BULLETIN_M1_S1, MediaCodeConstants::CODE_BULLETIN_M1_S2,
                MediaCodeConstants::CODE_BULLETIN_M2_S3, MediaCodeConstants::CODE_BULLETIN_M2_S4, MediaCodeConstants::CODE_BULLETIN_L1,
                MediaCodeConstants::CODE_BULLETIN_L2, MediaCodeConstants::CODE_BULLETIN_L3, MediaCodeConstants::CODE_BULLETIN_M1,
                MediaCodeConstants::CODE_BULLETIN_M2
                    => TypeConstants::HISTORY_TYPE_CV,
                MediaCodeConstants::CODE_ATTESTATION_ANGLAIS, MediaCodeConstants::CODE_ATTESTATION_MANAGEMENT, MediaCodeConstants::CODE_SUMMON
                    => TypeConstants::HISTORY_EPREUVES_ECRITES,
                default => TypeConstants::HISTORY_TYPE_DOCUMENT
            };
        }

        return $type;
    }

    /**
     * @param ObjectManager $om
     * @param class-string<object> $objectClass
     * @param mixed $objectId
     * @return Student|null
     */
    private function getStudent(ObjectManager $om, mixed $objectClass, mixed $objectId): ?Student
    {
        $repo = $om->getRepository($objectClass);

        if (method_exists($objectClass, 'getStudent')) {
            /** @phpstan-ignore-next-line */
            return $repo->find($objectId)->getStudent();
        }

        if (method_exists($objectClass, 'getSender')) {
            /** @phpstan-ignore-next-line */
            $sender = $repo->find($objectId)->getSender();
            if (null === $sender) {
                return null;
            }

            /** @phpstan-ignore-next-line */
            return $repo->find($objectId)->getSender()->getStudent() ?? null;
        }

        if (method_exists($objectClass, 'getAdministrativeRecord')) {
            /** @var AdministrativeRecord $administrativeRecord */
            /** @phpstan-ignore-next-line */
            $administrativeRecord = $repo->find($objectId)->getAdministrativeRecord();
            if (null != $administrativeRecord) {
                return $administrativeRecord->getStudent();
            }
        }

        return (method_exists($repo, 'findStudent'))? $repo->findStudent($objectId) : null;
    }
}