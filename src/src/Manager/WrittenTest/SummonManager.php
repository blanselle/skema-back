<?php

declare(strict_types=1);

namespace App\Manager\WrittenTest;

use App\Constants\Bloc\BlocConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Notification\NotificationConstants;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamStudent;
use App\Entity\Exam\ExamSummon;
use App\Entity\Media;
use App\Entity\Notification\Notification;
use App\Entity\User;
use App\Message\WrittenTest\SummonMessage;
use App\Repository\Exam\ExamStudentRepository;
use App\Repository\Exam\ExamSummonRepository;
use App\Service\Bloc\BlocRewriter;
use App\Service\Media\MediaSummonsPathGenerator;
use App\Service\Notification\NotificationCenter;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment as Twig;

class SummonManager
{
    public function __construct(
        private Pdf $pdf,
        private EntityManagerInterface $em,
        private BlocRewriter $blocRewriter,
        private Twig $twig,
        private MediaSummonsPathGenerator $mediaSummonsPathGenerator,
        private ExamStudentRepository $examStudentRepository,
        private ExamSummonRepository $examSummonRepository,
        private NotificationCenter $notificationCenter,
        private LoggerInterface $writtenTestSummonLogger,
        private MessageBusInterface $bus,
    ){}

    public function sendSummons(ExamClassification $examClassification, User $user): void
    {
        $this->log('START', [$examClassification]);

        $examStudents = $this->examStudentRepository->getExamStudentsInternByExamClassification($examClassification);
        
        foreach ($examStudents as $examStudent) {
            $this->bus->dispatch(new SummonMessage(examStudentId: $examStudent->getId()));
        }
        $this->log('dispatch notification BO');
        $this->dispatchNotificationForBo($examClassification, $user);
    }

    public function sendSummon(ExamStudent $examStudent): void
    {
        $examClassification = $examStudent->getExamSession()->getExamClassification();

        $header = $this->twig->render('exam/pdf/_header.html.twig');
        
        $this->log($examStudent->getStudent()->getIdentifier(), [$examStudent]);

        $html = $this->twig->render('exam/pdf/summons.html.twig', $this->getParameter($examStudent));
        list($absolutePathFile, $relativePathFile) = $this->generatePaths($examClassification, $examStudent);

        if (file_exists($absolutePathFile)) {
            $this->log('unlink', [$examStudent]);
            unlink($absolutePathFile);
        }
        $this->pdf->generateFromHtml($html, $absolutePathFile, ['header-html' => $header]);
        
        $examSummon = $this->examSummonRepository->findOneBy(['examStudent' => $examStudent]);
        if(null === $examSummon) {
            $this->log('create examSummon', [$examStudent]);
            $examSummon = $this->createExamSummon(examStudent: $examStudent, path: $relativePathFile);

            $this->em->persist($examSummon);
            $this->em->flush();            
        }

        $this->log('dispatch notification', [$examStudent]);
        $this->dispatchNotificationForStudent($examStudent, $examClassification);
    }

    private function getParameter(ExamStudent $examStudent): array
    {
        $zone1 = $this->blocRewriter->rewriteBloc(
            bloc: 'SUMMONS_ZONE_1',
            params: [
                'exam_date_start' => \IntlDateFormatter::formatObject(
                    $examStudent->getExamSession()->getDateStart(),
                    'EEEE d MMMM y à HH:mm'
                ),
                'typologie' => $examStudent->getExamSession()->getExamClassification()->getName(),
            ],
        );
        $zone2 = $this->blocRewriter->rewriteBloc(bloc: 'SUMMONS_ZONE_2');
        $zoneEquipment = $this->blocRewriter->rewriteBloc(bloc: 'SUMMONS_ZONE_EQUIPMENT');
        $zone3 = $this->blocRewriter->rewriteBloc(bloc: 'SUMMONS_ZONE_3');

        return [
            'examStudent' => $examStudent,
            'date' => \IntlDateFormatter::formatObject(new DateTimeImmutable(), 'd MMMM y'),
            'zone_1' => $zone1->getContent(),
            'zone_2' => $zone2->getContent(),
            'zone_equipment' => $zoneEquipment->getContent(),
            'zone_3' => $zone3->getContent(),
        ];
    }

    private function createExamSummon(ExamStudent $examStudent, string $path): ExamSummon
    {
        $originalName = substr($path, (int)strrpos($path, '/') +1);
        
        return (new ExamSummon())
            ->setStudent($examStudent->getStudent())
            ->setExamSession($examStudent->getExamSession())
            ->setMedia((new Media())
                ->setStudent($examStudent->getStudent())
                ->setFile($path)
                ->setOriginalName($originalName)
                ->setCode(MediaCodeConstants::CODE_SUMMON)
            )
            ->setExamStudent($examStudent)
        ;
    }

    private function dispatchNotificationForStudent(ExamStudent $examStudent, ExamClassification $examClassification): void
    {
        $bloc = $this->blocRewriter->rewriteBloc(
            bloc: BlocConstants::BLOC_NOTIFICATION_SUMMONS_GENERATED,
            params: [
                'nom_typologie' => $examClassification->getName(),
                'firstname' => $examStudent->getStudent()->getUser()->getFirstName()
            ],
        );

        $this->notificationCenter->dispatch(
            (new Notification())
                ->setSubject($bloc->getLabel())
                ->setContent($bloc->getContent())
                ->setReceiver($examStudent->getStudent()->getUser())
            ,
            [
                NotificationConstants::TRANSPORT_DB
            ],
            sendGenericMail: false,
        );
    }

    private function dispatchNotificationForBo(ExamClassification $examClassification, User $user): void
    {
        $this->notificationCenter->dispatch(
            (new Notification())
                ->setSubject('Generation convocation terminée')
                ->setContent(sprintf('La génération des convocation %s est terminée', $examClassification->getName()))
                ->setReceiver($user)
            ,
            [
                NotificationConstants::TRANSPORT_DB
            ],
            sendGenericMail: false,
        );
    }

    private function generatePaths(ExamClassification $examClassification, ExamStudent $examStudent): array
    {
        return [
            $this->mediaSummonsPathGenerator->getAbsoluteMediaSummonsPath(
                $examClassification,
                $examStudent->getExamSession(),
                $examStudent->getStudent()
            ),
            $this->mediaSummonsPathGenerator->getRelativeMediaSummonsPath(
                $examClassification,
                $examStudent->getExamSession(),
                $examStudent->getStudent()
            ),
        ];
    }

    private function log(string $message, array $context = []): void
    {
        $this->writtenTestSummonLogger->debug(sprintf('WrittentTestSummon : %s', $message), $context);
    }
}
