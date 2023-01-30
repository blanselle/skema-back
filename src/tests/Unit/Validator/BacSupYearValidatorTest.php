<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\CV\Bac\Bac;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\Student;
use App\Repository\CV\BacSupRepository;
use App\Service\Cv\BacSupLevel;
use App\Validator\Bac\BacTypeCount;
use App\Validator\Cv\BacSupYear;
use App\Validator\Cv\BacSupYearValidator;
use DateTime;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacSupYearValidatorTest extends TestCase
{
    private BacSupLevel|MockObject $bacSupLevel;
    private BacSupRepository|MockObject $bacSupRepository;
    private ExecutionContextInterface|MockObject $context;

    private BacSupYearValidator $bacSupYearValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bacSupRepository = $this->createMock(BacSupRepository::class);
        $this->bacSupLevel = $this->createMock(BacSupLevel::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        
        $this->bacSupYearValidator = new BacSupYearValidator($this->bacSupLevel, $this->bacSupRepository);
        
        $this->bacSupYearValidator->initialize($this->context);
    }

    public function testBacSupYearValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->never())->method('getObject');
        $this->bacSupRepository->expects($this->never())->method('findBy');
        $this->bacSupLevel->expects($this->never())->method('get');
        $this->expectException(UnexpectedTypeException::class);
        $this->bacSupYearValidator->validate(new DateTime(), new BacTypeCount());
    }

    public function testBacSup1OutdatedGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->bacSupProvider())
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(1)
        ;

        $this->bacSupYearValidator->validate(2018, new BacSupYear());
    }

    public function testBacSup1EqualsDateIsOK(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->bacSupProvider())
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(1)
        ;

        $this->bacSupYearValidator->validate(2020, new BacSupYear());
    }

    public function testBacSup1IsOK(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->bacSupProvider())
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(1)
        ;

        $this->bacSupYearValidator->validate(2022, new BacSupYear());
    }

    public function testBacSup1WithoutBacGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->bacSupWithourBacProvider())
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(1)
        ;

        $this->bacSupYearValidator->validate(2010, new BacSupYear());
    }

    public function testBacSup1WithoutBacIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn($this->bacSupWithourBacProvider())
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(1)
        ;

        $this->bacSupYearValidator->validate(2030, new BacSupYear());
    }

    public function testBacSup2GetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new BacSup())
                ->setCv((new Cv())
                
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([(new BacSup())
                ->setYear(2022)
            ])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(2)
        ;

        $this->bacSupYearValidator->validate(2018, new BacSupYear());
    }

    public function testBacSup2IsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new BacSup())
                ->setCv((new Cv())
                
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([(new BacSup())
                ->setYear(2015)
            ])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(2)
        ;

        $this->bacSupYearValidator->validate(2018, new BacSupYear());
    }

    public function testBacSup0GetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new BacSup())
                ->setCv((new Cv())
                
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([(new BacSup())
                ->setYear(2015)
            ])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(0)
        ;

        $this->expectException(Exception::class);

        $this->bacSupYearValidator->validate(2018, new BacSupYear());
    }

    public function testExistingBacSup2GetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new BacSup())
                ->setCv((new Cv())
                
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([
                (new BacSup())
                    ->setYear(2015)
                ,
            ])
        ;

        $this->bacSupLevel
            ->expects($this->once())
            ->method('get')
            ->willReturn(2)
        ;

        $this->bacSupYearValidator->validate(2015, new BacSupYear());
    }

    private function bacSupProvider(): BacSup
    {
        return (new BacSup())
            ->setCv((new Cv())
                ->setBac((new Bac())
                    ->setRewardedYear(2020)
                )
            )
        
        ;
    }

    private function bacSupWithourBacProvider(): BacSup
    {
        return (new BacSup())
            ->setCv((new Cv())
                ->setStudent((new Student())
                    ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2008-01-01'))
                )
            )
        
        ;
    }
}