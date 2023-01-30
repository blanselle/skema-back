<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility\Rule;

use App\Constants\CV\Experience\ExperienceStateConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Entity\Admissibility\Bonus\Category;
use App\Entity\Admissibility\Bonus\ExperienceBonus;
use App\Entity\CV\Cv;
use App\Entity\CV\Experience;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\ExperienceBonusRepository;
use App\Ruler\CV\Rule\ExperienceRule;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExperienceRuleTest extends TestCase
{
    private ExperienceBonusRepository|MockObject $experienceBonusRepository;

    private ExperienceRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->experienceBonusRepository = $this->createMock(ExperienceBonusRepository::class);

        $this->rule = new ExperienceRule(
            $this->experienceBonusRepository,
        );
    }

    public function testGetExperienceBonusIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $bonus = (new ExperienceBonus())
            ->setCategory((new Category())
                ->setName('Experience')
                ->setKey('experience')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setType(ExperienceTypeConstants::TYPE_ASSOCIATIVE)
            ->setDuration(3)
        ;

        $student = (new Student())
            ->setCv((new Cv())
                ->addExperience((new Experience())
                    ->setExperienceType(ExperienceTypeConstants::TYPE_ASSOCIATIVE)
                    ->setDuration(50)
                )
                ->addExperience((new Experience())
                    ->setExperienceType(ExperienceTypeConstants::TYPE_PROFESSIONAL)
                    ->setDuration(1)
                )
                ->addExperience((new Experience())
                    ->setExperienceType(ExperienceTypeConstants::TYPE_PROFESSIONAL)
                    ->setExperienceType(ExperienceStateConstants::STATE_REJECTED)
                    ->setDuration(1)
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->experienceBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.5, $this->rule->getBonus($student));
    }

    public function testGetExperienceBonusWithoutExperienceIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $bonus = (new ExperienceBonus())
            ->setCategory((new Category())
                ->setName('Experience')
                ->setKey('experience')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setType(ExperienceTypeConstants::TYPE_ASSOCIATIVE)
            ->setDuration(3)
        ; 

        $student = (new Student())
            ->setCv((new Cv())
                ->addExperience((new Experience())
                    ->setExperienceType(ExperienceTypeConstants::TYPE_INTERNATIONAL)
                    ->setDuration(2)
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->experienceBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetExperienceBonusUnCompleteCvGetAnError(): void
    {
        $student = (new Student())
            ->setCv(null)
        ;

        $this->experienceBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }
}