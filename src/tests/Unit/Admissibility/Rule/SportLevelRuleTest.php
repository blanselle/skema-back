<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility\Rule;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\AdministrativeRecord\SportLevel;
use App\Entity\Admissibility\Bonus\Category;
use App\Entity\Admissibility\Bonus\SportLevelBonus;
use App\Entity\Media;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\SportLevelBonusRepository;
use App\Ruler\CV\Rule\SportLevelRule;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SportLevelRuleTest extends TestCase
{
    private SportLevelBonusRepository|MockObject $sportLevelBonusRepository;

    private SportLevelRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sportLevelBonusRepository = $this->createMock(SportLevelBonusRepository::class);

        $this->rule = new SportLevelRule(
            $this->sportLevelBonusRepository,
        );
    }

    public function testGetSportLevelBonusIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $sportLevel = (new SportLevel())
            ->setLabel('SP')
        ;

        $bonus = (new SportLevelBonus())
            ->setCategory((new Category())
                ->setName('Niveau sportif')
                ->setKey('sport_level')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setSportLevel($sportLevel)
        ; 

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setSportLevel($sportLevel)
                ->addHighLevelSportsmanMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->sportLevelBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.5, $this->rule->getBonus($student));
    }

    public function testGetSportLevelBonusNoMediaGet0(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $sportLevel = (new SportLevel())
            ->setLabel('SP')
        ;

        $bonus = (new SportLevelBonus())
            ->setCategory((new Category())
                ->setName('Niveau sportif')
                ->setKey('sport_level')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setSportLevel($sportLevel)
        ; 

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setSportLevel($sportLevel)
            )
            ->setProgramChannel($programChannel)
        ;

        $this->sportLevelBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetSportLevelBonusNoValidMediaGet0(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $sportLevel = (new SportLevel())
            ->setLabel('SP')
        ;

        $bonus = (new SportLevelBonus())
            ->setCategory((new Category())
                ->setName('Niveau sportif')
                ->setKey('sport_level')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setSportLevel($sportLevel)
        ; 

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setSportLevel($sportLevel)
                ->addHighLevelSportsmanMedia((new Media())
                    ->setType(MediaWorflowStateConstants::STATE_REJECTED)
                )
            )
            ->setProgramChannel($programChannel)
        ;

        $this->sportLevelBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetSportLevelBonusWithoutSportLevelIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $sportLevel = (new SportLevel())
            ->setLabel('SP')
        ;

        $bonus = (new SportLevelBonus())
            ->setCategory((new Category())
                ->setName('Niveau sportif')
                ->setKey('sport_level')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setSportLevel($sportLevel)
        ; 

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setSportLevel((new SportLevel()))
            )
            ->setProgramChannel($programChannel)
        ;

        $this->sportLevelBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetSportLevelWrongProgramChannel(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $sportLevel = (new SportLevel())
            ->setLabel('SP')
        ;

        $bonus = (new SportLevelBonus())
            ->setCategory((new Category())
                ->setName('Niveau sportif')
                ->setKey('sport_level')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setSportLevel($sportLevel)
        ; 

        $student = (new Student())
            ->setAdministrativeRecord((new AdministrativeRecord())
                ->setSportLevel($sportLevel)
            )
            ->setProgramChannel(new ProgramChannel())
        ;

        $this->sportLevelBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->sportLevelBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus])
        ;

        $this->assertSame(0.0, $this->rule->getBonus($student));
    }

    public function testGetSportLevelBonusWithoutAdministrativeRecordGetAnError(): void
    {
        $student = (new Student())
            ->setAdministrativeRecord(null)
        ;

        $this->sportLevelBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }
}