<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Constants\Exam\ExamSessionTypeCodeConstants;
use App\Entity\Exam\ExamClassification;
use App\Entity\Exam\ExamSession;
use App\Entity\Exam\ExamSessionType;
use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Entity\User;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Repository\Parameter\ParameterRepository;
use App\Validator\Bac\BacOption;
use App\Validator\ExamSession\ExamSessionDate;
use App\Validator\ExamSession\ExamSessionDateValidator;
use DateTime;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ExamSessionDateValidatorTest extends TestCase
{
    private ParameterRepository|MockObject $parameterRepository;
    private Security|MockObject $security;
    private ExecutionContextInterface|MockObject $context;

    private ExamSessionDateValidator $examSessionDateValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parameterRepository = $this->createMock(ParameterRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        
        $this->examSessionDateValidator = new ExamSessionDateValidator($this->parameterRepository, $this->security);
        
        $this->examSessionDateValidator->initialize($this->context);
    }

    public function testBacTypeCountValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->never())->method('getObject');
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyNameAndProgramChannel');
        $this->security->expects($this->never())->method('getUser');
        $this->expectException(UnexpectedTypeException::class);
        $this->examSessionDateValidator->validate(new DateTime(), new BacOption());
    }

    public function testExamSessionDateInvalidParameterNotFoundGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->examSessionProvider())
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn(null)
        ;

        $this->expectException(ParameterNotFoundException::class);
        $this->examSessionDateValidator->validate(new DateTime(), new ExamSessionDate());
    }

    public function testExamSessionDateInvalidParameterValueGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->examSessionProvider())
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn((new Parameter())
                ->setValue('3')
            )
        ;

        $this->expectException(Exception::class);
        $this->examSessionDateValidator->validate(new DateTime(), new ExamSessionDate());
    }

    public function testExamSessionDateAnglaisInvalidGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->examSessionProvider())
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->exactly(2))
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn(
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2020-01-01')),
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2022-01-01')),
            )
        ;

        $this->examSessionDateValidator->validate(date_create_from_format('Y-m-d', '2024-01-01'), new ExamSessionDate());
    }

    public function testExamSessionDateManagementInvalidGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');

        $examSession = $this->examSessionProvider();
        $examSession->getExamClassification()->getExamSessionType()->setCode(ExamSessionTypeCodeConstants::MANAGEMENT);
        $examSession->getExamClassification()->setName('GMAT®');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($examSession)
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->exactly(2))
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn(
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2020-01-01')),
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2022-01-01')),
            )
        ;

        $this->examSessionDateValidator->validate(date_create_from_format('Y-m-d', '2024-01-01'), new ExamSessionDate());
    }

    public function testExamSessionDateAnglaisIsValid(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->examSessionProvider())
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->exactly(2))
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn(
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2020-01-01')),
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2022-01-01')),
            )
        ;

        $this->examSessionDateValidator->validate(date_create_from_format('Y-m-d', '2021-01-01'), new ExamSessionDate());
    }

    public function testExamSessionDateManangementIsValid(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $examSession = $this->examSessionProvider();
        $examSession->getExamClassification()->getExamSessionType()->setCode(ExamSessionTypeCodeConstants::MANAGEMENT);
        $examSession->getExamClassification()->setName('TEST');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($examSession)
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->exactly(2))
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn(
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2020-01-01')),
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2022-01-01')),
            )
        ;

        $this->examSessionDateValidator->validate(date_create_from_format('Y-m-d', '2021-01-01'), new ExamSessionDate());
    }

    public function testExamSessionDateManangementExceptionIsValid(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $examSession = $this->examSessionProvider();
        $examSession->getExamClassification()->getExamSessionType()->setCode(ExamSessionTypeCodeConstants::MANAGEMENT);
        $examSession->getExamClassification()->setName('GMAT®');
        
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($examSession)
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;
        
        $this->parameterRepository
            ->expects($this->exactly(2))
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn(
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2020-01-01')),
                (new Parameter())
                    ->setValue(date_create_from_format('Y-m-d', '2022-01-01')),
            )
        ;

        $this->examSessionDateValidator->validate(date_create_from_format('Y-m-d', '2021-01-01'), new ExamSessionDate());
    }

    public function testExamSessionDateOtherExceptionIsValid(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $examSession = $this->examSessionProvider();
        $examSession->getExamClassification()->getExamSessionType()->setCode('Other');
        
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyNameAndProgramChannel');

        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($examSession)
        ;
        
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($this->userProvider())
        ;

        $this->examSessionDateValidator->validate(date_create_from_format('Y-m-d', '2021-01-01'), new ExamSessionDate());
    }

    private function examSessionProvider(): ExamSession
    {
        return (new ExamSession())
            ->setExamClassification((new ExamClassification())
                ->setExamSessionType((new ExamSessionType())
                    ->setCode(ExamSessionTypeCodeConstants::ANG)
                )
        );
    }

    private function userProvider(): User
    {
        return (new User())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                
                )
        );
    }
}