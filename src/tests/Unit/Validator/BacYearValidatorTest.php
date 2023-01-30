<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\CV\Bac\Bac;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\Student;
use App\Repository\CV\BacSupRepository;
use App\Validator\Bac\BacTypeCount;
use App\Validator\Cv\BacYear;
use App\Validator\Cv\BacYearValidator;
use DateTime;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacYearValidatorTest extends TestCase
{
    private BacSupRepository|MockObject $bacSupRepository;
    private ExecutionContextInterface|MockObject $context;

    private BacYearValidator $bacYearValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bacSupRepository = $this->createMock(BacSupRepository::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        
        $this->bacYearValidator = new BacYearValidator($this->bacSupRepository);
        
        $this->bacYearValidator->initialize($this->context);
    }

    public function testBacYearValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->never())->method('getObject');
        $this->bacSupRepository->expects($this->never())->method('findBy');
        $this->expectException(UnexpectedTypeException::class);
        $this->bacYearValidator->validate(new DateTime(), new BacTypeCount());
    }

    public function testBacYearGreaterThanBacSupWithBacSupGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Bac())
                ->setCv((new Cv())
                    ->setStudent((new Student())
                        ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                    )
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(
                (new BacSup())
                    ->setYear(2020)
            )
        ;

        $this->bacYearValidator->validate(2021, new BacYear());
    }

    public function testBacYearLessThanUserBirthYearWithBacSupGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Bac())
                ->setCv((new Cv())
                    ->setStudent((new Student())
                        ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2018-01-01'))
                    )
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->bacYearValidator->validate(2015, new BacYear());
    }

    public function testBacYearWithBacSupIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Bac())
                ->setCv((new Cv())
                    ->setStudent((new Student())
                        ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                    )
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(
                (new BacSup())
                    ->setYear(2020)
            )
        ;

        $this->bacYearValidator->validate(2018, new BacYear());
    }

    public function testBacYearWithEqualDateBacSupIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Bac())
                ->setCv((new Cv())
                    ->setStudent((new Student())
                        ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                    )
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(
                (new BacSup())
                    ->setYear(2020)
            )
        ;

        $this->bacYearValidator->validate(2020, new BacYear());
    }

    public function testBacYearWithUpperDateBacSupIsOk(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Bac())
                ->setCv((new Cv())
                    ->setStudent((new Student())
                        ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                    )
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(
                (new BacSup())
                    ->setYear(2020)
            )
        ;

        $this->bacYearValidator->validate(2021, new BacYear());
    }

    public function testBacYearWithoutBacSupIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Bac())
                ->setCv((new Cv())
                    ->setStudent((new Student())
                        ->setDateOfBirth(DateTime::createFromFormat('Y-m-d', '2010-01-01'))
                    )
                )
            )
        ;

        $this->bacSupRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->bacYearValidator->validate(2018, new BacYear());
    }
}