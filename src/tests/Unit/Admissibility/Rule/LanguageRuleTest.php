<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility\Rule;

use App\Entity\Admissibility\Bonus\Category;
use App\Entity\Admissibility\Bonus\LanguageBonus;
use App\Entity\CV\Cv;
use App\Entity\CV\Language;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\LanguageBonusRepository;
use App\Ruler\CV\Rule\LanguageRule;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LanguageRuleTest extends TestCase
{
    private LanguageBonusRepository|MockObject $languageBonusRepository;

    private LanguageRule $rule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->languageBonusRepository = $this->createMock(LanguageBonusRepository::class);

        $this->rule = new LanguageRule(
            $this->languageBonusRepository,
        );
    }

    public function testGetLanguageBonusIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $bonus1 = (new LanguageBonus())
            ->setCategory((new Category())
                ->setName('Langue')
                ->setKey('language')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.8)
            ->setMin(2)
        ;
            
        $bonus2 = (new LanguageBonus())
            ->setCategory((new Category())
                ->setName('Langue')
                ->setKey('language')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setMin(0)
        ;

        $student = (new Student())
            ->setCv((new Cv())
                ->addLanguage(new Language())
                ->addLanguage(new Language())
                ->addLanguage(new Language())
            )
            ->setProgramChannel($programChannel)
        ;

        $this->languageBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus1, $bonus2])
        ;

        $this->assertSame(0.8, $this->rule->getBonus($student));
    }

    public function testGetLanguageBonusWithoutLanguageIsOk(): void
    {
        $programChannel = (new ProgramChannel())
            ->setName('AST 1')
        ;

        $bonus1 = (new LanguageBonus())
            ->setCategory((new Category())
                ->setName('Langue')
                ->setKey('language')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.8)
            ->setMin(2)
        ;
            
        $bonus2 = (new LanguageBonus())
            ->setCategory((new Category())
                ->setName('Langue')
                ->setKey('language')
            )
            ->setProgramChannel($programChannel)
            ->setValue(0.5)
            ->setMin(0)
        ;

        $student = (new Student())
            ->setCv((new Cv())
            )
            ->setProgramChannel($programChannel)
        ;

        $this->languageBonusRepository
            ->expects($this->once())
            ->method('findByCategory')
            ->willReturn([$bonus1, $bonus2])
        ;

        $this->assertSame(0.5, $this->rule->getBonus($student));
    }

    public function testGetLanguageBonusUnCompleteCvGetAnError(): void
    {
        $student = (new Student())
            ->setCv(null)
        ;

        $this->languageBonusRepository->expects($this->never())->method('findByCategory');
        $this->expectException(Exception::class);
        $this->rule->getBonus($student);
    }
}