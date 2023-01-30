<?php

declare(strict_types=1);

namespace App\Tests\Unit\Admissibility;

use App\Constants\CV\BacSupConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\ProgramChannel\ProgramChannelKeyConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\Cv;
use App\Entity\CV\SchoolReport;
use App\Entity\Media;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Ruler\CV\CvRuler;
use App\Service\Admissibility\Cv\CvCalculator;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CvCalculatorTest extends TestCase
{
    private CvCalculator $cvCalculator;
    private CvRuler|MockObject $cvRuler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cvRuler = $this->createMock(CvRuler::class);

        $this->cvCalculator = new CvCalculator($this->cvRuler);
    }

    public function testCvCalculatorWithoutMedia(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setScore(15)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setScore(16.5)
            )
            ->addSchoolReport((new SchoolReport())
                ->setScore(14.5)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(10)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(20)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(5.0, $cv->getNote());
    }

    public function testCvCalculatorWithMediaNotRejected(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_TO_CHECK)
                )
                ->setScore(15)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_TO_CHECK)
                )
                ->setScore(16.5)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_REJECTED)
                )
                ->setScore(14.5)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(10)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(20)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(5.0, $cv->getNote());
    }

    public function testCvCalculator1(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(16.5)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(14.5)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.2)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.6)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(15.5, $cv->getNote());
    }

    public function testCvCalculator2(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(16.5)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(14.5)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.2)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.6)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST2)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(15.3, round($cv->getNote(), 2));
    }

    public function testCvCalculator3(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.4)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(12.7)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(14.8)
            )
        ;

        // Double parcourt

        $bacSups[] = (new BacSup())
            ->setParent($bacSups[0])
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(12)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setParent($bacSups[1])
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(14)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setParent($bacSups[2])
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(14)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST2)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(14.3, round($cv->getNote(), 2));
    }

    public function testCvCalculator4(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(17.4)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(16.3)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(16.1)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.8)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(17.2)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15.9)
            )
        ;

        // Double parcourt

        $bacSups[] = (new BacSup())
            ->setParent($bacSups[0])
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(0.0)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(0.0)
            )
        ;
        $bacSups[] = (new BacSup())
            ->setParent($bacSups[1])
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(0.0)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(0.0)
            )
        ;
        $bacSups[] = (new BacSup())
            ->setParent($bacSups[2])
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(0.0)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(0.0)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(16.85, round($cv->getNote(), 2));
    }

    public function testCvCalculator5(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(20)
                ->setScoreRetained(19)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(12)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15)
                ->setScoreRetained(16)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(15)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST2)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(16.5, $cv->getNote());
    }

    public function testCvCalculatorOk(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(1)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(2)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(3)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(4)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(5)
            )
        ;

        // Double diplome
        $bacSups[] = (new BacSup())
            ->setParent(end($bacSups))
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(6)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(8)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(3.0, round($cv->getNote(), 2));
        $this->assertNotNull($cv->getNote());
    }

    public function testCvCalculatorWithAst2IsOk(): void
    {
        $bacSups = [];

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(10)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(20)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_ANNUAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(3)
            )
        ;

        $bacSups[] = (new BacSup())
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(4)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(5)
            )
        ;

        // Double diplome
        $bacSups[] = (new BacSup())
            ->setParent(end($bacSups))
            ->setType(BacSupConstants::TYPE_SEMESTRIAL)
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(6)
            )
            ->addSchoolReport((new SchoolReport())
                ->setMedia((new Media())
                    ->setState(MediaWorflowStateConstants::STATE_ACCEPTED)
                )
                ->setScore(8)
            )
        ;

        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST2)
                )
            )
            ->setBacSups(new ArrayCollection($bacSups))
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(9.0, round($cv->getNote(), 2));
        $this->assertNotNull($cv->getNote());
    }

    public function testCvCalculatorNoBacSupCvGetAnError(): void
    {
        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(0.0, $cv->getNote());
    }

    public function testCvCalculatorNoSchoolReportGetAnError(): void
    {
        $cv = (new Cv())
            ->setStudent((new Student())
                ->setProgramChannel((new ProgramChannel())
                    ->setKey(ProgramChannelKeyConstants::AST1)
                )
            )
            ->addBacSup((new BacSup())
                ->setType(BacSupConstants::TYPE_ANNUAL)

            )
            ->addBacSup((new BacSup())
                ->setType(BacSupConstants::TYPE_ANNUAL)
            )
        ;

        $this->cvCalculator->updateCvNotes($cv);

        $this->assertSame(0.0, $cv->getNote());
    }
}