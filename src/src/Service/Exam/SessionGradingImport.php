<?php

declare(strict_types=1);

namespace App\Service\Exam;

use App\Constants\Bloc\BlocConstants;
use App\Constants\Notification\NotificationConstants;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamStudent;
use App\Entity\User;
use App\Exception\ExamStudent\CollisionException;
use App\Exception\ExamStudent\ExamStudentNotFoundException;
use App\Exception\InvalidDateTimeFormaException;
use App\Manager\NotificationManager;
use App\Repository\Exam\ExamStudentRepository;
use App\Service\Notification\NotificationCenter;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Security;

class SessionGradingImport
{
    private const CSV_SESSION_TYPE = 0;
    private const CSV_SESSION_DATE = 1;
    private const CSV_CANDIDATE_LASTNAME = 2;
    private const CSV_CANDIDATE_FIRSTNAME = 3;
    private const CSV_CANDIDATE_BIRTHDATE = 4;
    private const CSV_SESSION_GRADE = 5;
    private const FORMAT_DATE = 'd/m/Y';
    private const CSV_SEPARATOR = ';';
    
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationManager $notificationManager,
        private NotificationCenter $notificationCenter,
        private Security $security,
        private ExamStudentRepository $examStudentRepository,
    ) {
    }

    public function execute(File $file, ExamClassification $examClassification, array &$errors): array
    {
        $data = $this->extractDataFromFile($file, $examClassification);

        return $this->treatData($data, $examClassification, $errors);
    }

    public function confirmImport(ExamClassification $examClassification, string $filepath, array &$errors): array
    {
        $file = new File($filepath);
        $data = $this->extractDataFromFile($file, $examClassification);

        $examStudents = $this->treatData($data, $examClassification, $errors);
        $this->entityManager->flush();
        $this->sendNotificationToAdministrationUser(count($examStudents), $errors, $examClassification->getName());
        $this->sendNotificationToEachCandidateWichHasImportedScore($examClassification, $examStudents);

        return $examStudents;
    }

    private function checkConformityFileData(array $data, ExamClassification $examClassification): bool
    {
        if(trim(strtolower($data[self::CSV_SESSION_TYPE])) !== strtolower($examClassification->getName())) {
            return false;
        }

        return true;
    }

    private function extractDataFromFile(File $file, ExamClassification $examClassification): array
    {
        $i = 0;
        $data = [];
        $handle = fopen($file->getPathname(), "r");
        if (false !== $handle) {
            while (($line = fgetcsv($handle, null, self::CSV_SEPARATOR)) !== false) {
                if ($i !== 0) {
                    if (!$this->checkConformityFileData($line, $examClassification)) {
                        throw new BadRequestException(
                            "Fichier Incorrect : La typologie de l'épreuve et/ou la date de déroulement de l'épreuve ne correspondent pas."
                        );
                    }
                    $data[] = $line;
                }
                $i++;
            }
            fclose($handle);
        }

        return $data;
    }

    private function getExamStudent(
        ExamClassification $examClassification,
        string $lastName,
        string $firstName,
        DateTimeInterface $date,
        ?string $birth = null,
    ): ExamStudent {

        $dateOfBirth = null;
        if(null !== $birth) {
            $dateOfBirth = DateTime::createFromFormat(self::FORMAT_DATE, $birth);

            if(!$dateOfBirth instanceof DateTime) {
                throw new InvalidDateTimeFormaException();
            }
        }

        $examStudent = $this->examStudentRepository->findExamStudentWithIdentityByExamClassification(
            $examClassification,
            $lastName,
            $firstName,
            $date,
            $dateOfBirth,
        );
        
        if(null === $examStudent) {
            throw new ExamStudentNotFoundException();
        }

        return $examStudent;
    }

    private function treatData(array $data, ExamClassification $examClassification, array &$errors): array
    {
        $examStudents = [];
        foreach ($data as $lineNumber => $item) {
            $examStudent = null;
            $lastName = $item[self::CSV_CANDIDATE_LASTNAME];
            $firstName = $item[self::CSV_CANDIDATE_FIRSTNAME];
            $dateOfBirth = $item[self::CSV_CANDIDATE_BIRTHDATE];
            $score = (int)$item[self::CSV_SESSION_GRADE];

            $date = DateTime::createFromFormat(self::FORMAT_DATE, $item[self::CSV_SESSION_DATE]);
            if(!$date instanceof DateTime) {

                $errors[] = sprintf(
                    'Ligne %d : Le format de la date de passage de %s %s est invalide',
                    $lineNumber,
                    $lastName,
                    $firstName,
                );

                continue;
            }

            try {
                $examStudent = $this->getExamStudent(
                    $examClassification, 
                    $lastName, 
                    $firstName, 
                    $date,
                    $dateOfBirth,
                );
            } catch(ExamStudentNotFoundException $e) {
                $errors[] = sprintf(
                    'Ligne %d : Impossible de trouver la session de %s %s',
                    $lineNumber,
                    $lastName,
                    $firstName,
                );

                continue;

            } catch(InvalidDateTimeFormaException $e) {

                $errors[] = sprintf(
                    'Ligne %d : Le format de la date de naissance de %s %s est invalide',
                    $lineNumber,
                    $lastName,
                    $firstName,
                );

                continue;
            } catch (CollisionException $e) {
                $errors[] = $e->getMessage();
            }

            $examStudent->setScore($score);

            $examStudents[] = $examStudent;
        }

        return $examStudents;
    }

    private function sendNotificationToAdministrationUser(int $nbLines, array $errors, string $examClassifcationName): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $notification = $this->notificationManager->createNotification(
            receiver: $user,
            blocKey: BlocConstants::BLOC_NOTIFICATION_IMPORT_SCORES_DONE,
            params: [
                'nb_lines' => (string)$nbLines,
                'errors' => '<br />' . implode('<br />', $errors),
                'exam_classification' => $examClassifcationName,
            ]
        );

        $this->notificationCenter->dispatch(
            $notification, 
            [
                NotificationConstants::TRANSPORT_EMAIL,
                NotificationConstants::TRANSPORT_DB
            ], 
            sendGenericMail: false
        );
    }

    private function sendNotificationToEachCandidateWichHasImportedScore(ExamClassification $examClassification, array $examStudents): void
    {
        $students = [];
        foreach ($examStudents as $examStudent) {
            
            $student = $examStudent->getStudent();
            if(isset($students[$student->getId()])) {
                continue;
            }
            $students[$examStudent->getId()] = $student;

            $notification = $this->notificationManager->createNotification(
                receiver: $student->getUser(),
                blocKey: BlocConstants::BLOC_NOTIFICATION_IMPORT_SCORES_NOTIFICATION_STUDENT,
                params: [
                    'nom_typologie' => $examClassification->getName(),
                    'firstname' => $student->getUser()->getFirstName()
                ]
            );

            $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_DB]);
        }
    }
}
