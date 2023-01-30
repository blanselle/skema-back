<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Parameter\Parameter;
use App\Entity\ProgramChannel;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Repository\Parameter\ParameterRepository;
use App\Repository\ProgramChannelRepository;
use App\Validator\Bac\BacOption;
use App\Validator\Parameter\LessThanParameter;
use App\Validator\Parameter\LessThanParameterValidator;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ComparatorParameterValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $context;
    private ParameterRepository|MockObject $parameterRepository;
    private ProgramChannelRepository|MockObject $programChannelRepository;

    private LessThanParameterValidator $lessThanParameter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->parameterRepository = $this->createMock(ParameterRepository::class);
        $this->programChannelRepository = $this->createMock(ProgramChannelRepository::class);
        
        $this->lessThanParameter = new LessThanParameterValidator($this->parameterRepository, $this->programChannelRepository);
        
        $this->lessThanParameter->initialize($this->context);
    }

    public function testComparatorParameterCountValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyNameAndProgramChannel');
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyName');
        $this->expectException(UnexpectedTypeException::class);
        $this->lessThanParameter->validate(new \DateTime(), new BacOption());
    }

    public function testBacTypeCountValidatorWithProgramChannelIsOk(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyName');
        $this->programChannelRepository->expects($this->once())->method('find')->willReturn(new ProgramChannel());
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyNameAndProgramChannel')
            ->willReturn((new Parameter())
                ->setValue(new DateTime())
            )
        ;
        $this->context->expects($this->once())->method('getPropertyName')->willReturn('zerzerzer');

        $this->lessThanParameter->validate((new DateTime())->modify('+1 year'), new LessThanParameter('test', '3'));
    }
    
    public function testBacTypeCountValidatorWithoutProgramChannelIsOk(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyNameAndProgramChannel');
        $this->programChannelRepository->expects($this->once())->method('find')->willReturn(null);
        $this->parameterRepository
            ->expects($this->once())
            ->method('findOneParameterByKeyName')
            ->willReturn((new Parameter())
                ->setValue(new DateTime())
            )
        ;
        $this->context->expects($this->once())->method('getPropertyName')->willReturn('zerzerzer');

        $this->lessThanParameter->validate((new DateTime())->modify('+1 year'), new LessThanParameter('test', '3'));
    }

    public function testBacTypeCountValidatorWithoutProgramChannelNotFoundIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->parameterRepository->expects($this->never())->method('findOneParameterByKeyNameAndProgramChannel');
        $this->programChannelRepository->expects($this->once())->method('find')->willReturn(null);
        $this->parameterRepository->expects($this->once())->method('findOneParameterByKeyName')->willReturn(null);
        $this->context->expects($this->never())->method('getPropertyName');

        $this->expectException(ParameterNotFoundException::class);
        $this->lessThanParameter->validate((new DateTime())->modify('+1 year'), new LessThanParameter('test', '3'));
    }
}