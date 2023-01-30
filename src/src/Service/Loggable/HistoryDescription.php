<?php

namespace App\Service\Loggable;

use App\Constants\Exam\ExamConditionConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Constants\Loggable\FieldsConstants;
use App\Entity\AdministrativeRecord\ScholarShipLevel;
use App\Entity\AdministrativeRecord\SportLevel;
use App\Entity\Admissibility\LandingPage\AdmissibilityStudentToken;
use App\Entity\Country;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\Bac\BacChannel;
use App\Entity\CV\Bac\BacDistinction;
use App\Entity\CV\Bac\BacOption;
use App\Entity\CV\Bac\BacType;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\Language;
use App\Entity\CV\SchoolReport;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Exam\ExamLanguage;
use App\Entity\Exam\ExamStudent;
use App\Entity\Loggable\History;
use App\Entity\Media;
use App\Entity\Notification\Notification;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Helper\StringHelper;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Contracts\Translation\TranslatorInterface;

class HistoryDescription
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator
    ) {}

    public function getDescription(History|LogEntry $log, string $key, mixed $value): string
    {
        $description = $this->translator->trans("loggable.action.{$log->getAction()}") . " : ";
        $className = StringHelper::getClassName($log->getObjectClass());
        $description .= "[{$className}] ";
        $field = StringHelper::camelCaseToSnakeCase($key);
        $description .= $this->translator->trans("loggable.field.{$field}") . " => ";

        $description .= match ($log->getObjectClass()) {
            Student::class => $this->getDescriptionForStudent(key: $key, value: $value),
            Cv::class => $this->getDescriptionForCv(key: $key, value: $value),
            Bac::class => $this->getDescriptionForBac(key: $key, value: $value),
            BacSup::class => $this->getDescriptionForBacSup(key: $key, value: $value),
            AdministrativeRecord::class => $this->getDescriptionForAdministrativeRecord(key: $key, value: $value),
            StudentDiploma::class => $this->getDescriptionForStudentDiploma(key: $key, value: $value),
            Payment::class => $this->getDescriptionForPayment(key: $key, value: $value, objectId: $log->getObjectId()),
            Order::class => $this->getDescriptionForOrder(key: $key, value: $value, objectId: $log->getObjectId()),
            ExamStudent::class => $this->getDescriptionForExamStudent(key: $key, value: $value, objectId: $log->getObjectId()),
            Notification::class => $this->getDescriptionForNotification(key: $key, value: $value, objectId: $log->getObjectId()),
            SchoolReport::class => $this->getDescriptionForSchoolReport(key: $key, value: $value, objectId: $log->getObjectId()),
            Media::class => $this->getDescriptionForMedia(key: $key, value: $value, objectId: $log->getObjectId()),
            AdmissibilityStudentToken::class => $this->getDescriptionForAdmissibilityStudentToken(key: $key, value: $value, objectId: $log->getObjectId()),
            default => $this->getDefault(value: $value)
        };

        return $description;
    }

    private function getDefault(mixed $value): ?string
    {
        if (is_bool($value)) {
            return $value ? 'Oui' : 'Non';
        }
        if ($value instanceof DateTimeInterface) {
            return $value->format('m/d/Y H:m:i');
        }

        if (is_array($value) || is_object($value)) {
            return serialize($value);
        }

        return $value;
    }

    private function getDescriptionForStudent(string $key, mixed $value): ?string
    {
        return match($key) {
            'country', 'countryBirth' => $this->entityManager->getRepository(Country::class)->find($value['id'])?->getName(),
            'nationality', 'nationalitySecondary' => $this->entityManager->getRepository(Country::class)->find($value['id'])?->getNationality(),
            'state' => $this->translator->trans("workflow.student.{$value}"),
            'programChannel' => $this->translator->trans("loggable.history.message.programChannel", [
                '%programChannel%' => $this->entityManager->getRepository(ProgramChannel::class)->find($value['id'])?->getName()
            ]),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForCv(string $key, mixed $value): ?string
    {
        return match($key) {
            'noProfessionalExperience', 'noAssociativeExperience', 'noInternationnalExperience' => (bool)$value? 'Oui' : 'Non',
            'languages' => $this->getCollectionDescription(class: Language::class, value:  $value),
            default => $this->getDefault(value: $value)
        };
    }

    /**
     * @param class-string<object> $class
     * @param mixed $value
     * @return string
     */
    private function getCollectionDescription(mixed $class, mixed $value): string
    {
        $repository = $this->entityManager->getRepository($class);
        $str = '';
        $keys = ['add' => '(+) ', 'remove' => '(-) '];
        foreach ($keys  as $k => $prefix) {
            $str .= "\n";
            if (!empty($value[$k]?? [])) {
                $str .= $prefix;
                foreach ($value[$k] as $id) {
                    $object = $repository->find($id);
                    if (method_exists($object, 'getLabel')) {
                        /** @phpstan-ignore-next-line */
                        $str .= $object?->getLabel() . ', ';
                    } elseif (method_exists($object, 'getName')) {
                        /** @phpstan-ignore-next-line */
                        $str .= $object?->getName() . ', ';
                    }
                }
                $str = rtrim($str, ', ');
            }
        }

        return $str;
    }

    private function getDescriptionForBac(string $key, mixed $value): ?string
    {
        return match($key) {
            'bacDistinction' => $this->entityManager->getRepository(BacDistinction::class)->find($value['id'])?->getLabel(),
            'bacChannel' => $this->entityManager->getRepository(BacChannel::class)->find($value['id'])?->getName(),
            'bacOption' => $this->entityManager->getRepository(BacOption::class)->find($value['id'])?->getName(),
            'bacTypes' => $this->getCollectionDescription(class: BacType::class, value: $value),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForBacSup(string $key, mixed $value): ?string
    {
        return match($key) {
            'diploma' => $this->entityManager->getRepository(Diploma::class)->find($value['id'])?->getName(),
            'diplomaChannel' => $this->entityManager->getRepository(DiplomaChannel::class)->find($value['id'])?->getName(),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForAdministrativeRecord(string $key, mixed $value): ?string
    {
        return match($key) {
            'sportLevel' => $this->entityManager->getRepository(SportLevel::class)->find($value['id'])?->getLabel(),
            'jdc' => $this->entityManager->getRepository(Media::class)->find($value['id'])?->getOriginalName(),
            'examLanguage' => $this->entityManager->getRepository(ExamLanguage::class)->find($value['id'])?->getName(),
            'scholarShipLevel' => $this->entityManager->getRepository(ScholarShipLevel::class)->find($value['id'])?->getLabel(),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForStudentDiploma(string $key, mixed $value): ?string
    {
        return match($key) {
            'diplomaChannel' => $this->entityManager->getRepository(DiplomaChannel::class)->find($value['id'])?->getName(),
            'diploma' => $this->entityManager->getRepository(Diploma::class)->find($value['id'])?->getName(),
            'dualPathStudentDiploma' => !empty($value) ? 'Oui' : 'Non',
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForPayment(string $key, mixed $value, string $objectId): ?string
    {
        $payment = $this->entityManager->getRepository(Payment::class)->find((int)$objectId);

        return match($key) {
            'state' => $this->translator->trans("workflow.payment.{$payment->getState()}") . ' pour ' . $this->translator->trans("order.type.{$payment->getIndent()->getType()}", ['%session_name%' => $payment->getIndent()->getExamSession()?->getExamClassification()->getName()]),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForOrder(string $key, mixed $value, string $objectId): ?string
    {
        $order = $this->entityManager->getRepository(Order::class)->find((int)$objectId);

        return match($key) {
            'state' => $this->translator->trans("workflow.order.{$order->getState()}") . ' pour ' . $this->translator->trans("order.type.{$order->getType()}", ['%session_name%' => $order->getExamSession()?->getExamClassification()->getName()]),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForMedia(string $key, mixed $value, string $objectId): string
    {
        $media = $this->entityManager->getRepository(Media::class)->find((int) $objectId);
        $code = (null !== $media?->getCode())? strtolower($media->getCode()) : null;
        $code = (null !== $code)? $this->translator->trans("media.codes.{$code}") : null;
        $str = (null !== $code)? "({$code}) " : "";
        $str .= match($key) {
            'state' => $this->translator->trans("workflow.media.{$value}"),
            default => $this->getDefault(value: $value)
        };

        return $str;
    }

    private function getDescriptionForExamStudent(string $key, mixed $value, string $objectId): ?string
    {
        /** @var ExamStudent $examStudent */
        $examStudent = $this->entityManager->getRepository(ExamStudent::class)->find((int)$objectId);
        if (null == $examStudent) {
            return $this->getDefault(value: $value);
        }

        return match($key){
            'examSession' => $this->translator->trans("examStudent.inscription", [
                '%firstName%' => $examStudent->getStudent()->getUser()->getFirstName(),
                '%lastName%' => $examStudent->getStudent()->getUser()->getLastName(),
                '%examSessionName%' => $examStudent->getExamSession()->getExamClassification()->getName(),
                '%examSessionStart%' => $examStudent->getExamSession()->getDateStart()->format('d-m-Y H:i'),
                '%campus%' => (!empty($examStudent->getExamSession()->getCampus())) ? $examStudent->getExamSession()->getCampus()->getName() : ExamConditionConstants::CONDITION_ONLINE
            ]),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForNotification(string $key, mixed $value, string $objectId): ?string
    {
        /** @var Notification $notification */
        $notification = $this->entityManager->getRepository(Notification::class)->find((int)$objectId);

        return match($key) {
            'subject' => $this->translator->trans("notification.sender", [
                '%firstName%' => $notification->getSender()->getFirstName(),
                '%lastName%' => $notification->getSender()->getLastName(),
                '%subject%' => $notification->getSubject()
            ]),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForSchoolReport(string $key, mixed $value, string $objectId): ?string
    {
        /** @var SchoolReport $schoolReport */
        $schoolReport = $this->entityManager->getRepository(SchoolReport::class)->find((int)$objectId);
        if (null == $schoolReport) {
            return $this->getDefault(value: $value);
        }

        return match($key) {
            'bacSup' => $this->translator->trans("cv.schoolReport", [
                '%firstName%' => $schoolReport->getCv()->getStudent()->getUser()->getFirstName(),
                '%lastName%' => $schoolReport->getCv()->getStudent()->getUser()->getLastName(),
                '%diploma%' => $schoolReport->getBacSup()->getDiploma()->getName(),
                '%year%' => $schoolReport->getBacSup()->getYear(),
                '%establishement%' => $schoolReport->getBacSup()->getEstablishment()
            ]),
            default => $this->getDefault(value: $value)
        };
    }

    private function getDescriptionForAdmissibilityStudentToken(string $key, mixed $value, string $objectId): string
    {
        return FieldsConstants::HISTORY_ADMISSIBILITY_TOKEN;
    }
}
