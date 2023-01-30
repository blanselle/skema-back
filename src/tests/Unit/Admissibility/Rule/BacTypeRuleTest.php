<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility\Rule;

use App\Entity\Admissibility\Bonus\BacTypeBonus;
use App\Entity\Admissibility\Bonus\Category;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\Bac\BacType;
use App\Entity\CV\Cv;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\BacTypeBonusRepository;
use App\Ruler\CV\Rule\BacTypeRule;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BacTypeRuleTest extends TestCase
{
    private BacTypeBonusRepository|MockObject $bacTypeBonusRepository;

    private BacTypeRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bacTypeBonusRepository = $this->createMock(BacTypeBonusRepository::class);

        $this->rule = new BacTypeRule(
            $this->bacTypeBonusRepository,
        );
    }

    public function testGetBacTypeBonusIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $bacType = (new BacType())
            ->setName('S')
        ;

        $bonus = (new BacTypeBonus())
            ->setCategory((new Category())
                ->setName('Type de bac')
                ->setKey('bac_type')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setBacType($bacType)
        ;

        $student = (new Student())
            ->setCv((new Cv())
                ->setBac((new Bac())
                    ->addBacType(new BacType())
                    ->addBacType($bacType)
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->bacTypeBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.5, $this->rule->getBonus($student));
    }

    public function testGetBacTypeBonusWithoutBacTypeIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $bacType = (new BacType())
            ->setName('S')
        ;

        $bonus = (new BacTypeBonus())
            ->setCategory((new Category())
                ->setName('Type de bac')
                ->setKey('bac_type')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setBacType($bacType)
        ; 

        $student = (new Student())
            ->setCv((new Cv())
                ->setBac((new Bac())
                    ->addBacType(new BacType())
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->bacTypeBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetBacTypeBonusUnCompleteCvGetAnError(): void
    {
        $student = (new Student())
            ->setCv(null)
        ;

        $this->bacTypeBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }

    public function testGetBacTypeBonusWithoutBacGetAnError(): void {
        $student = (new Student())
            ->setCv((new Cv())
                ->setBac(null)
            )
        ;

        $this->bacTypeBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }
}