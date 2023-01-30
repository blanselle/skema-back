<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Exam;

use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamStudent;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\NotificationManager;
use App\Repository\Exam\ExamStudentRepository;
use App\Service\Exam\SessionGradingImport;
use App\Service\Notification\NotificationCenter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File as UploadedFile;
use Symfony\Component\Security\Core\Security;

class SessionGradingImportTest extends TestCase
{
    private const TEST_FILE = __DIR__ . '/../../../uploads/example_import_ecrit.csv';
    private EntityManagerInterface|MockObject $em;
    private NotificationManager|MockObject $notificationManager;
    private NotificationCenter|MockObject $notificationCenter;
    private Security|MockObject $security;
    private ExamStudentRepository|MockObject $examStudentRepository;
    
    private SessionGradingImport $sessionGradingImport;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->notificationManager = $this->createMock(NotificationManager::class);
        $this->notificationCenter = $this->createMock(NotificationCenter::class);
        $this->security = $this->createMock(Security::class);
        $this->examStudentRepository = $this->createMock(ExamStudentRepository::class);

        $this->sessionGradingImport = new SessionGradingImport(
            $this->em,
            $this->notificationManager,
            $this->notificationCenter,
            $this->security,
            $this->examStudentRepository,
        );
    }

    public function testExecuteGradingImport(): void
    {
        $examClassification = (new ExamClassification())
            ->setKey('toiec')
            ->setName('TOEIC®')
        ;

        $examStudent = (new ExamStudent())
            ->setScore(10)    
        ;

        $errors = [];

        $this->examStudentRepository
            ->expects($this->exactly(3))
            ->method('findExamStudentWithIdentityByExamClassification')
            ->willReturn($examStudent, null, null)
        ;

        $this->em->expects($this->never())->method('flush');
        $this->notificationCenter->expects($this->never())->method('dispatch');

        $this->sessionGradingImport->execute(
            (new UploadedFile(self::TEST_FILE)),
            $examClassification,
            $errors,
        );

        $this->assertCount(4, $errors);

        $this->assertSame(510.0, $examStudent->getScore());
    }

    public function testConfirmGradingImport(): void
    {
        $examClassification = (new ExamClassification())
            ->setKey('toiec')
            ->setName('TOEIC®')
        ;

        $examStudent1 = (new ExamStudent())
            ->setScore(10)
            ->setStudent((new Student())
                ->setId(1)
                ->setUser((new User())
                    ->setFirstName('firstname')
                    ->setLastName('lastname')
                )
            )
        ;

        $examStudent2 = (new ExamStudent())
            ->setScore(20)
            ->setStudent((new Student())
                ->setId(2)
                ->setUser((new User())
                    ->setFirstName('firstname')
                    ->setLastName('lastname')
                )
            )
        ;

        $this->em->expects($this->once())->method('flush');
        $this->notificationCenter->expects($this->exactly(3))->method('dispatch');

        $errors = [];

        $this->examStudentRepository
            ->expects($this->exactly(3))
            ->method('findExamStudentWithIdentityByExamClassification')
            ->willReturn($examStudent1, null, $examStudent2)
        ;

        $this->sessionGradingImport->confirmImport(
            $examClassification,
            self::TEST_FILE,
            $errors,
        );

        $this->assertCount(3, $errors);

        $this->assertSame(510.0, $examStudent1->getScore());
        $this->assertSame(511.0, $examStudent2->getScore()); 
    }
}
