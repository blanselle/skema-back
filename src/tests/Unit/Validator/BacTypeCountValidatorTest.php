<?php

declare(strict_types=1);

namespace App\Tests\Unit\Validator;

use App\Constants\CV\BacChannelConstants;
use App\Constants\CV\TagBacConstants;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\Bac\BacChannel;
use App\Entity\CV\Bac\BacType;
use App\Service\Cv\GetTypeBacFromYear;
use App\Validator\Bac\BacOption;
use App\Validator\Bac\BacTypeCount;
use App\Validator\Bac\BacTypeCountValidator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class BacTypeCountValidatorTest extends TestCase
{
    private GetTypeBacFromYear|MockObject $getTypebacFromYear;
    private ExecutionContextInterface|MockObject $context;

    private BacTypeCountValidator $bacTypeCountValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getTypebacFromYear = $this->createMock(GetTypebacFromYear::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        
        $this->bacTypeCountValidator = new BacTypeCountValidator($this->getTypebacFromYear);
        
        $this->bacTypeCountValidator->initialize($this->context);
    }

    public function testBacTypeCountValidatorWithInvalidObjectGetAnError(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->expectException(UnexpectedTypeException::class);
        $this->bacTypeCountValidator->validate(new \DateTime(), new BacOption());
    }

    public function testBacTypeCountValidatorWithoutBacChannelIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->once())->method('getObject')->willReturn(new Bac());
        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorWithoutRewardedYearIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');
        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())
                ->setKey(BacChannelConstants::GENERAL)
            )
        );
        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacProWithInvalidNbacTypeGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())
                ->setKey(BacChannelConstants::PROFESSIONAL)
            )
            ->setRewardedYear(2021)
            ->setBacTypes(new ArrayCollection([new BacType()]))
        );

        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacV1WithInvalidBacType(): void
    {
        $this->context->expects($this->once())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())
            ->setKey(BacChannelConstants::GENERAL)
            )
            ->setBacTypes(new ArrayCollection([new BacType(), new BacType()]))
            ->setRewardedYear(2021)
        );

        $this->getTypebacFromYear->expects($this->once())->method('get')->willReturn(TagBacConstants::V1);
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacV2WithInvalidBacType(): void
    {
        $this->context->expects($this->once())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())
                ->setKey(BacChannelConstants::GENERAL)
            )
            ->setBacTypes(new ArrayCollection([new BacType()]))
            ->setRewardedYear(2021)
        );

        $this->getTypebacFromYear->expects($this->once())->method('get')->willReturn(TagBacConstants::V2);
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacV2IsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())    
                ->setKey(BacChannelConstants::GENERAL)
            )
            ->setBacTypes(new ArrayCollection([new BacType(), new BacType()]))
            ->setRewardedYear(2021)
        );

        $this->getTypebacFromYear->expects($this->once())->method('get')->willReturn(TagBacConstants::V2);
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacV1IsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())    
                ->setKey(BacChannelConstants::GENERAL)
            )
            ->setBacTypes(new ArrayCollection([new BacType()]))
            ->setRewardedYear(2021)
        );

        $this->getTypebacFromYear->expects($this->once())->method('get')->willReturn(TagBacConstants::V1);
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacProIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())    
                ->setKey(BacChannelConstants::PROFESSIONAL)
            )
            ->setRewardedYear(2021)
        );

        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacTechnoIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())    
                ->setKey(BacChannelConstants::TECHNOLOGIE)
            )
            ->setBacTypes(new ArrayCollection([new BacType()]))
        );

        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorBacTechnoInvalideBacTypeGetAnError(): void
    {
        $this->context->expects($this->once())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())    
                ->setKey(BacChannelConstants::TECHNOLOGIE)
            )
        );

        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }

    public function testBacTypeCountValidatorWithOtherBacChannelKeyIsOk(): void
    {
        $this->context->expects($this->never())->method('buildViolation');

        $this->context->expects($this->once())->method('getObject')->willReturn((new Bac())
            ->setBacChannel((new BacChannel())    
                ->setKey('other key')
            )
            ->setBacTypes(new ArrayCollection([new BacType()]))
        );

        $this->getTypebacFromYear->expects($this->never())->method('get');
        $this->bacTypeCountValidator->validate(new \DateTime, new BacTypeCount());
    }
}