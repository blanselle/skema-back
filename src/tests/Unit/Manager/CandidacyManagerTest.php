<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager;

use App\Constants\Exam\ExamSessionTypeNameConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamSessionType;
use App\Entity\Exam\ExamStudent;
use App\Entity\Media;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\Student;
use App\Manager\CandidacyManager;
use App\Repository\MediaRepository;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Monolog\Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CandidacyManagerTest extends TestCase
{
    private StudentWorkflowManager|MockObject $studentWorkflowManager;

    private MediaRepository $mediaRepository;

    private CandidacyManager $candidacyManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentWorkflowManager = $this->createMock(StudentWorkflowManager::class);
        $this->mediaRepository = $this->createMock(MediaRepository::class);

        $this->candidacyManager = new CandidacyManager(
            $this->studentWorkflowManager,
            $this->mediaRepository
        );
    }

    public function testSchoolRegistrationFeesWithPaymentIsDone(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->addOrder((new Order())
                ->setState(OrderWorkflowStateConstants::STATE_VALIDATED)
                ->setType(OrderTypeConstants::SCHOOL_REGISTRATION_FEES)
                ->setAmount(1000)
                ->addPayment((new Payment())
                    ->setState(PaymentWorkflowStateConstants::STATE_VALIDATED)
                )
            )
        ;

        $result = $this->candidacyManager->schoolRegistration($student);

        $this->assertSame('done', $result);
    }

    public function testSchoolRegistrationFeesWithValidArAndNotPayedAndStateCreatedToPayIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(false)
        ;

        $student = (new Student())
            ->setState(StudentWorkflowStateConstants::STATE_CREATED_TO_PAY)
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setJdc((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
            )
        ;

        $result = $this->candidacyManager->schoolRegistration($student);

        $this->assertSame('to_do', $result);
    }

    public function testSchoolRegistrationFeesStudentRejectedIsForbidden(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(true)
        ;

        $student = (new Student())
            ->setState(StudentWorkflowStateConstants::STATE_REJECTED)
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setJdc((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
            )
        ;

        $result = $this->candidacyManager->schoolRegistration($student);

        $this->assertSame('forbidden', $result);
    }
    
    public function testCvWithCvNullIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;
            
        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
        ;
        
        $result = $this->candidacyManager->cv($student);
        
        $this->assertSame('to_do', $result);
    }
    

    public function testCvIsNotValidalidatedIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
            ->setCv((new Cv())
                ->setValidated(false)
            )
        ;

        $result = $this->candidacyManager->cv($student);

        $this->assertSame('to_do', $result);
    }

    public function testCvWithoutBacIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
            ->setCv((new Cv())
                ->setValidated(true)
            )
        ;

        $result = $this->candidacyManager->cv($student);

        $this->assertSame('to_do', $result);
    }

    public function testCvBacMediaRefusedIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
            ->setCv((new Cv())
                ->setValidated(true)
                ->setBac((new Bac())
                    ->setMedia((new Media())
                        ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                    )
                )
            )
        ;

        $result = $this->candidacyManager->cv($student);

        $this->assertSame('to_do', $result);
    }

    public function testCvBacSupMediaRefusedIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->addOrder((new Order())
                ->setState(OrderWorkflowStateConstants::STATE_VALIDATED)
                ->setType(OrderTypeConstants::SCHOOL_REGISTRATION_FEES)
                ->setAmount(1000)
                ->addPayment((new Payment())
                    ->setState(PaymentWorkflowStateConstants::STATE_VALIDATED)
                )
            )
            ->setCv((new Cv())
                ->setValidated(true)
                ->setBac((new Bac())
                    ->setMedia((new Media())
                        ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                    )
                )
                ->addBacSup((new BacSup())
                    ->addSchoolReport((new SchoolReport())
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                        )
                    )
                )
            )
        ;

        $result = $this->candidacyManager->cv($student);

        $this->assertSame('to_do', $result);
    }

    public function testCvDoneIsDone(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
            ->setCv((new Cv())
                ->setValidated(true)
                ->setBac((new Bac())
                    ->setMedia((new Media())
                        ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                    )
                )
                ->addBacSup((new BacSup())
                    ->addSchoolReport((new SchoolReport())
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                        )
                    )
                )
            )
        ;

        $result = $this->candidacyManager->cv($student);

        $this->assertSame('done', $result);
    }

    public function testCvWithNoPaymentAndNoScholarShipIsForbidden(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(false)
            )
            ->setCv((new Cv())
                ->setValidated(true)
                ->setBac((new Bac())
                    ->setMedia((new Media())
                        ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                    )
                )
                ->addBacSup((new BacSup())
                    ->addSchoolReport((new SchoolReport())
                        ->setMedia((new Media())
                            ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                        )
                    )
                )
            )
        ;

        $result = $this->candidacyManager->cv($student);

        $this->assertSame('forbidden', $result);
    }

    public function testWrittenExaminationPaidAndScoredDoneIsOk(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $examSession = (new ExamSession())
            ->setExamClassification((new ExamClassification())
                ->setExamSessionType((new ExamSessionType())
                    ->setName(ExamSessionTypeNameConstants::TYPE_ENGLISH)
                )
            );
        $examSession2 = (new ExamSession())
            ->setExamClassification((new ExamClassification())
                ->setExamSessionType((new ExamSessionType())
                    ->setName(ExamSessionTypeNameConstants::TYPE_MANAGEMENT)
                )
            );

        $student = (new Student())
            ->addOrder((new Order())
                ->setState(OrderWorkflowStateConstants::STATE_VALIDATED)
                ->setType(OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION)
                ->setAmount(1000)
                ->setExamSession($examSession)
                ->addPayment((new Payment())
                    ->setState(PaymentWorkflowStateConstants::STATE_VALIDATED)
                )
            )
            ->addOrder((new Order())
                ->setState(OrderWorkflowStateConstants::STATE_VALIDATED)
                ->setType(OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION)
                ->setAmount(1000)
                ->setExamSession($examSession2)
                ->addPayment((new Payment())
                    ->setState(PaymentWorkflowStateConstants::STATE_VALIDATED)
                )
            )
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
            ->addExamStudent((new ExamStudent())
                ->setExamSession($examSession)
            )
            ->addExamStudent((new ExamStudent())
                ->setExamSession($examSession2)
                ->setScore(1)
            )
        ;

        $result = $this->candidacyManager->writtenExamination($student);

        $this->assertSame('done', $result);
    }

    /**
     * https://pictime.atlassian.net/browse/SB-1328
     */
    public function testWrittenExaminationWithoutPaidIsDoneIsOk(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $examSession = (new ExamSession())
            ->setExamClassification((new ExamClassification())
                ->setExamSessionType((new ExamSessionType())
                    ->setName(ExamSessionTypeNameConstants::TYPE_ENGLISH)
                )
            );
        $examSession2 = (new ExamSession())
            ->setExamClassification((new ExamClassification())
                ->setExamSessionType((new ExamSessionType())
                    ->setName(ExamSessionTypeNameConstants::TYPE_MANAGEMENT)
                )
            );

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
            ->addExamStudent((new ExamStudent())
                ->setExamSession($examSession)
            )
            ->addExamStudent((new ExamStudent())
                ->setExamSession($examSession2)
                ->setScore(1)
            )
        ;

        $result = $this->candidacyManager->writtenExamination($student);

        $this->assertSame('done', $result);
    }

    public function testWrittenExaminationScholShipStudentIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(true)
            )
        ;

        $result = $this->candidacyManager->writtenExamination($student);

        $this->assertSame('to_do', $result);
    }

    public function testWrittenExaminationPaymentRefusedIsForbidden(): void
    {
        $this->studentWorkflowManager
            ->expects($this->never())
            ->method('isBeingRegistered')
        ;

        $student = (new Student())
            ->addOrder((new Order())
                ->setState(OrderWorkflowStateConstants::STATE_CREATED)
                ->setType(OrderTypeConstants::SCHOOL_REGISTRATION_FEES)
                ->setAmount(1000)
                ->addPayment((new Payment())
                    ->setState(PaymentWorkflowStateConstants::STATE_REJECTED)
                )
            )
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setScholarShip(false)
            )
        ;

        $result = $this->candidacyManager->writtenExamination($student);

        $this->assertSame('forbidden', $result);
    }

    public function testAdministrativeRecordValidatedIsDone(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(false)
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setJdc((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
            )
        ;

        $result = $this->candidacyManager->administrativeRecord($student);

        $this->assertSame('done', $result);
    }

    public function testAdministrativeRecordNotCreatedStudentIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(true)
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord()));

        $result = $this->candidacyManager->administrativeRecord($student);

        $this->assertSame('to_do', $result);
    }

    public function testAdministrativeRecordCreatedStudentIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(true)
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord()))
            ->setState(StudentWorkflowStateConstants::STATE_CREATED);

        $result = $this->candidacyManager->administrativeRecord($student);

        $this->assertSame('to_do', $result);
    }

    public function testAdministrativeRecordMedia1RejectedIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(true)
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->addHighLevelSportsmanMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                )
            )
        ;

        $result = $this->candidacyManager->administrativeRecord($student);

        $this->assertSame('to_do', $result);
    }

    public function testAdministrativeRecordMedia2RejectedIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(true)
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->addThirdTimeMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                )
            )
        ;

        $result = $this->candidacyManager->administrativeRecord($student);

        $this->assertSame('to_do', $result);
    }

    public function testAdministrativeRecordMedia3RejectedIsToDo(): void
    {
        $this->studentWorkflowManager
            ->expects($this->once())
            ->method('isBeingRegistered')
            ->willReturn(true)
        ;

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->addScholarShipMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                )
            )
        ;

        $result = $this->candidacyManager->administrativeRecord($student);

        $this->assertSame('to_do', $result);
    }
}