<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager\WrittenTest;

use App\Entity\Bloc\Bloc;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamStudent;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\WrittenTest\SummonManager;
use App\Repository\Exam\ExamStudentRepository;
use App\Repository\Exam\ExamSummonRepository;
use App\Service\Bloc\BlocRewriter;
use App\Service\Media\MediaSummonsPathGenerator;
use App\Service\Notification\NotificationCenter;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Snappy\Pdf;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Twig\Environment as Twig;

class SummonManagerTest extends KernelTestCase
{
    private Pdf|MockObject $pdf;
    private EntityManagerInterface|MockObject $em;
    private BlocRewriter|MockObject $blocRewriter;
    private Twig $twig;
    private MediaSummonsPathGenerator|MockObject $mediaSummonsPathGenerator;
    private ExamStudentRepository|MockObject $examStudentRepository;
    private ExamSummonRepository|MockObject $examSummonRepository;
    private NotificationCenter|MockObject $notificationCenter;
    private LoggerInterface|MockObject $logger;
    private MessageBusInterface|MockObject $bus;

    private SummonManager $summonManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdf = $this->createMock(Pdf::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->blocRewriter = $this->createMock(BlocRewriter::class);
        $this->twig = $this->getContainer()->get('twig');
        $this->mediaSummonsPathGenerator = $this->createMock(MediaSummonsPathGenerator::class);
        $this->examStudentRepository = $this->createMock(ExamStudentRepository::class);
        $this->examSummonRepository = $this->createMock(ExamSummonRepository::class);
        $this->notificationCenter = $this->createMock(NotificationCenter::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->bus = $this->createMock(MessageBusInterface::class);

        $this->summonManager = new SummonManager(
            $this->pdf,
            $this->em,
            $this->blocRewriter,
            $this->twig,
            $this->mediaSummonsPathGenerator,
            $this->examStudentRepository,
            $this->examSummonRepository,
            $this->notificationCenter,
            $this->logger,
            $this->bus,
        );
    }


    public function testDispatchSummonIsOk(): void
    {
        $examStudents = [
            $this->examStudentProvider(),
            $this->examStudentProvider(),
            $this->examStudentProvider(),
        ];

        $this->examStudentRepository
            ->expects($this->once())
            ->method('getExamStudentsInternByExamClassification')
            ->willReturn($examStudents)
        ;

        $this->bus->expects($this->exactly(count($examStudents)))->method('dispatch')->willReturn(
            new Envelope((object) array('1' => 'foo'))
        );

        $this->notificationCenter->expects($this->once())->method('dispatch');

        $this->summonManager->sendSummons($examStudents[0]->getExamSession()->getExamClassification(), new User());
    }

    public function testGenerateSummonIsOk(): void
    {
        $examStudent = $this->examStudentProvider();

        $this->mediaSummonsPathGenerator
            ->expects($this->once())
            ->method('getAbsoluteMediaSummonsPath')
            ->willReturn('/absolute')
        ;
     
        $this->mediaSummonsPathGenerator
        ->expects($this->once())
            ->method('getRelativeMediaSummonsPath')
            ->willReturn('/relative')
        ;

        $this->blocRewriter
            ->expects($this->exactly(5))
            ->method('rewriteBloc')
            ->willReturnCallback(function() {
                return (new Bloc())
                    ->setContent('content')
                ;
            })
        ;

        $this->summonManager->sendSummon($examStudent);
    }

    private function examStudentProvider(): ExamStudent
    {
        return (new ExamStudent())
            ->setId(4)
            ->setExamSession((new ExamSession())
                ->setDateStart(new DateTime())
                ->setDateEnd(new DateTime())
                ->setExamClassification((new ExamClassification())
                    ->setName('EXAM CLASSIFICATION')
                )
            )
            ->setStudent((new Student())
                ->setUser((new User())
                    ->setLastName('LASTNAME')
                    ->setFirstName('FIRSTNAME')
                )
                ->setAddress('ADDRESS')
                ->setPostalCode('POSTALCODE')
                ->setCity('CITY')
                ->setIdentifier('IDENTIFIER')
                ->setProgramChannel((new ProgramChannel)
                    ->setName('PROGRAM_CHANNEL')
                )
            )
        ;
    }
}