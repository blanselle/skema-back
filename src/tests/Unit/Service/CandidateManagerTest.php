<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\AdministrativeRecord\AdministrativeRecord;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Media;
use App\Entity\Student;
use App\Entity\User;
use App\Manager\StudentManager;
use App\Service\CandidateManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CandidateManagerTest extends TestCase
{
    private EntityManagerInterface|MockObject $em;
    private StudentManager|MockObject $studentManager;
    
    private CandidateManager $candidateManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->studentManager = $this->createMock(StudentManager::class);

        $this->candidateManager = new CandidateManager(
            $this->em,
            $this->studentManager,
        );    
    }

    public function provideStudentInCreatedState(): Student
    {
        return (new Student())
            ->setUser((new User())
                ->setEmail('test@email.fr')
            )
        ;
    }

    public function initAdministrativeRecord(Student $student): AdministrativeRecord
    {
        $administrativeRecord = (new AdministrativeRecord())
            ->setStudent($student)
            ->setHighLevelSportsman(false)
            ->setScholarShip(false)
            ->setThirdTime(false)
            ->setOptionalExamLanguage(false)
            ->setJdc((new Media()))
        ;

        $student->setAdministrativeRecord($administrativeRecord);

        return $administrativeRecord;
    }

    public function testHasOtherDiplomaReturnTrue(): void
    {
        $student = $this->provideStudentInCreatedState();
        $this->initAdministrativeRecord($student);

        $this->studentManager
            ->expects($this->once())
            ->method('getStudentLastDiploma')
            ->willReturn((new StudentDiploma())->setDiploma((new Diploma())->setNeedDetail(true)))
        ;

        $this->assertTrue($this->candidateManager->hasOtherDiploma($student));
    }

    public function testHasOtherDiplomaWithNotDiplomaReturnFalse(): void
    {
        $student = $this->provideStudentInCreatedState();
        $this->initAdministrativeRecord($student);

        $this->studentManager
            ->expects($this->once())
            ->method('getStudentLastDiploma')
            ->willReturn(null)
        ;

        $this->assertFalse($this->candidateManager->hasOtherDiploma($student));
    }

    public function testHasOtherDiplomaWithNoLastDiplomaReturnFalse(): void
    {
        $student = $this->provideStudentInCreatedState();
        $this->initAdministrativeRecord($student);

        $this->studentManager
            ->expects($this->once())
            ->method('getStudentLastDiploma')
            ->willReturn((new StudentDiploma())->setDiploma((new Diploma())->setNeedDetail(false)))
        ;

        $this->assertFalse($this->candidateManager->hasOtherDiploma($student));
    }
}