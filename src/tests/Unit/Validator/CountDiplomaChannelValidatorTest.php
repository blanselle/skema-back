<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Validator\Bac\BacTypeCount;
use App\Validator\Diploma\CountDiplomaChannel;
use App\Validator\Diploma\CountDiplomaChannelValidator;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CountDiplomaChannelValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $context;

    private CountDiplomaChannelValidator $countDiplomaChannelValidator;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->context = $this->createMock(ExecutionContextInterface::class);
        
        $this->countDiplomaChannelValidator = new CountDiplomaChannelValidator();
        
        $this->countDiplomaChannelValidator->initialize($this->context);
    }

    public function testCountDiplomaChannelValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->never())->method('getObject');
        $this->expectException(UnexpectedTypeException::class);
        $this->countDiplomaChannelValidator->validate(new DateTime(), new BacTypeCount());
    }

    public function testCountDiplomaChannelWithoutNeedDetailDiplomaIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Diploma())
                ->setNeedDetail(false)
                ->addDiplomaChannel((new DiplomaChannel()))
            )
        ;

        $this->countDiplomaChannelValidator->validate(new DateTime(), new CountDiplomaChannel());
    }


    public function testCountDiplomaChannelWitNeedDetailDiplomaIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Diploma())
                ->setNeedDetail(true)
            )
        ;

        $this->countDiplomaChannelValidator->validate(new DateTime(), new CountDiplomaChannel());
    }


    public function testCountDiplomaChannelWithoutNeedDetailDiplomaGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context
            ->expects($this->once())
            ->method('getObject')
            ->willReturn((new Diploma())
                ->setNeedDetail(false)
            )
        ;

        $this->countDiplomaChannelValidator->validate(new DateTime(), new CountDiplomaChannel());
    }
}