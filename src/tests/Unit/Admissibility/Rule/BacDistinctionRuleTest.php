<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility\Rule;

use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Admissibility\Bonus\BacDistinctionBonus;
use App\Entity\Admissibility\Bonus\Category;
use App\Entity\CV\Bac\Bac;
use App\Entity\CV\Bac\BacDistinction;
use App\Entity\CV\Cv;
use App\Entity\Media;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\BacDistinctionBonusRepository;
use App\Ruler\CV\Rule\BacDistinctionRule;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BacDistinctionRuleTest extends TestCase
{
    private BacDistinctionBonusRepository|MockObject $bacDistinctionBonusRepository;

    private BacDistinctionRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bacDistinctionBonusRepository = $this->createMock(BacDistinctionBonusRepository::class);

        $this->rule = new BacDistinctionRule(
            $this->bacDistinctionBonusRepository,
        );
    }

    public function testGetBacDistinctionBonusIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $distinctionB = new BacDistinction();
        $distinctionB->setCode(DistinctionCodeConstants::DISTINCTION_BIEN)
            ->setLabel('Bien');

        $bonus1 = (new BacDistinctionBonus())
            ->setCategory((new Category())
                ->setName('Mention')
                ->setKey('distinction')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setBacDistinction($distinctionB)
        ;

        $distinctionTB = new BacDistinction();
        $distinctionTB->setCode(DistinctionCodeConstants::DISTINCTION_TRES_BIEN)
            ->setLabel('Très Bien');

        $bonus2 = (new BacDistinctionBonus())
            ->setCategory((new Category())
                ->setName('Mention')
                ->setKey('distinction')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.8)
            ->setBacDistinction($distinctionTB)
        ;

        $student = (new Student())
            ->setCv((new Cv())
                ->setBac((new Bac())
                    ->setBacDistinction($distinctionTB)
                    ->setMedia((new Media())
                        ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                    )
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->bacDistinctionBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus1, $bonus2])
        ;

        $this->assertSame(0.8, $this->rule->getBonus($student));
    }

    public function testGetBacDistinctionBonusRejectedMediaIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $distinctionB = new BacDistinction();
        $distinctionB->setCode(DistinctionCodeConstants::DISTINCTION_BIEN)
            ->setLabel('Bien');

        $bonus1 = (new BacDistinctionBonus())
            ->setCategory((new Category())
                ->setName('Mention')
                ->setKey('distinction')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setBacDistinction($distinctionB)
        ;

        $distinctionTB = new BacDistinction();
        $distinctionTB->setCode(DistinctionCodeConstants::DISTINCTION_TRES_BIEN)
            ->setLabel('Très Bien');

        $bonus2 = (new BacDistinctionBonus())
            ->setCategory((new Category())
                ->setName('Mention')
                ->setKey('distinction')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.8)
            ->setBacDistinction($distinctionTB)
        ;

        $student = (new Student())
            ->setCv((new Cv())
                ->setBac((new Bac())
                    ->setBacDistinction($distinctionTB)
                    ->setMedia((new Media())
                        ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                    )
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->bacDistinctionBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus1, $bonus2])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetBacDistinctionBonusWithoutBacDistinctionIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $distinctionB = new BacDistinction();
        $distinctionB->setCode(DistinctionCodeConstants::DISTINCTION_BIEN)
            ->setLabel('Bien');

        $bonus = (new BacDistinctionBonus())
            ->setCategory((new Category())
                ->setName('Mention')
                ->setKey('distinction')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setBacDistinction($distinctionB)
        ;

        $distinctionAB = new BacDistinction();
        $distinctionAB->setCode(DistinctionCodeConstants::DISTINCTION_ASSEZ_BIEN)
            ->setLabel('Assez Bien');

        $student = (new Student())
            ->setCv((new Cv())
                ->setBac((new Bac())
                    ->setBacDistinction($distinctionAB)
                    ->setMedia((new Media()))
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->bacDistinctionBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetBacDistinctionBonusUnCompleteCvGetAnError(): void
    {
        $student = (new Student())
            ->setCv(null)
        ;

        $this->bacDistinctionBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }

    public function testGetBacDistinctionBonusWithoutBacGetAnError(): void
    {
        $student = (new Student())
            ->setCv((new Cv())
                ->setBac(null)
            )
        ;

        $this->bacDistinctionBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }
}