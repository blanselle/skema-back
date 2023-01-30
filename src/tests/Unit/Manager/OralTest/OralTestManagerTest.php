<?php

declare(strict_types=1);

namespace App\Tests\Unit\Manager\OralTest;

use App\Entity\Campus;
use App\Entity\Exam\ExamLanguage;
use App\Entity\OralTest\CampusOralDay;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Repository\OralTest\CampusOralDayRepository;
use App\Repository\OralTest\OralTestStudentRepository;
use App\Service\OralTest\OralTestManager;
use App\Service\Workflow\OralTest\OralTestStudentWorkflowManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OralTestManagerTest extends TestCase
{
    private EntityManagerInterface|MockObject $em;
    private CampusOralDayRepository|MockObject $campusOralDayRepository;
    private OralTestStudentWorkflowManager|MockObject $oralTestStudentWorkflowManager;
    private OralTestStudentRepository|MockObject $oralTestStudentRepository;

    private OralTestManager $oralTestManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->campusOralDayRepository = $this->createMock(CampusOralDayRepository::class);
        $this->oralTestStudentWorkflowManager = $this->createMock(OralTestStudentWorkflowManager::class);
        $this->oralTestStudentRepository = $this->createMock(OralTestStudentRepository::class);

        $this->oralTestManager = new OralTestManager(
            $this->em,
            $this->campusOralDayRepository,
            $this->oralTestStudentWorkflowManager,
            $this->oralTestStudentRepository,
        );
    }

    public function testSetNewOralTestStudent(): void
    {
        $student = (new Student())
            ->setProgramChannel((new ProgramChannel()))
        ;

        $date = (new DateTimeImmutable());

        $campus = (new Campus());

        $campusOralDay = (new CampusOralDay());

        $this->oralTestStudentRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null)
        ;

        $this->em->expects($this->never())->method('remove');
        $this->em->expects($this->once())->method('flush');

        $this->campusOralDayRepository
            ->expects($this->once())
            ->method('findOneSlot')
            ->willReturn($campusOralDay)
        ;

        $oralTestStudent = $this->oralTestManager->replaceOralTestStudent(
            $student,
            $date,
            $campus,
            (new ExamLanguage()),
            (new ExamLanguage()),
        );

        $this->assertSame($campusOralDay, $oralTestStudent->getCampusOralDay());
    }
}