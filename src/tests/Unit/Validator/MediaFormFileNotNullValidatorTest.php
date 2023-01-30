<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Entity\Media;
use App\Validator\Bac\BacTypeCount;
use App\Validator\Media\MediaFormFileNotNull;
use App\Validator\Media\MediaFormFileNotNullValidator;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MediaFormFileNotNullValidatorTest extends TestCase
{
    private ExecutionContextInterface|MockObject $context;

    private MediaFormFileNotNullValidator $mediaFormFileNotNullValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = $this->createMock(ExecutionContextInterface::class);
        
        $this->mediaFormFileNotNullValidator = new MediaFormFileNotNullValidator();
        
        $this->mediaFormFileNotNullValidator->initialize($this->context);
    }

    public function testMediaFormFileNotNullValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->never())->method('getObject');
        $this->expectException(UnexpectedTypeException::class);
        $this->mediaFormFileNotNullValidator->validate(new DateTime(), new BacTypeCount());
    }

    public function testMediaFormFileNotNullValidatorFormFileNullGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');
        $this->context->expects($this->once())->method('getObject')->willReturn(new Media());
        $this->mediaFormFileNotNullValidator->validate(null, new MediaFormFileNotNull());
    }

    public function testMediaFormFileNotNullValidatorFormFileNullIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->once())->method('getObject')->willReturn((new Media())->setFile('file'));
        $this->mediaFormFileNotNullValidator->validate(null, new MediaFormFileNotNull());
    }

    public function testMediaFormFileNotNullValidatorFormFileNotNullIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->once())->method('getObject')->willReturn(new Media());
        $this->mediaFormFileNotNullValidator->validate('test', new MediaFormFileNotNull());
    }
}