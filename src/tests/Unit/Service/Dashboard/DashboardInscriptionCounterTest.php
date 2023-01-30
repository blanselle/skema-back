<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Dashboard;

use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\ProgramChannel;
use App\Repository\StudentRepository;
use App\Service\Dashboard\DashboardInscriptionCounter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardInscriptionCounterTest extends TestCase
{
    private StudentRepository|MockObject $studentRepository;
    
    private DashboardInscriptionCounter $inscriptionCounter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentRepository = $this->createMock(StudentRepository::class);

        $this->inscriptionCounter = new DashboardInscriptionCounter(
            $this->studentRepository,
        );
    }

    public function testGetRowsIsOk(): void
    {
        $this->studentRepository
            ->expects($this->exactly(2))
            ->method('findNbInscriptionByState')
            ->willReturn([
                [
                    'state' => StudentWorkflowStateConstants::STATE_CREATED,
                    'count' => 6,
                ],
                [
                    'state' => StudentWorkflowStateConstants::STATE_REJECTED,
                    'count' => 3,
                ],
                [
                    'state' => StudentWorkflowStateConstants::STATE_EXEMPTION,
                    'count' => 3,
                ],
                [
                    'state' => StudentWorkflowStateConstants::STATE_START,
                    'count' => 2,
                ],
            ]);
        ;

        $rows = $this->inscriptionCounter->getRows([new ProgramChannel(), new ProgramChannel()]);
        
        foreach($rows as $row) {
            $this->assertCount(2, $row->getValues());
            $this->assertTrue(strlen($row->getLabel()) > 3);
        }

        $this->assertSame('Nombre total d\'inscrits', $rows[0]->getLabel());
        $this->assertSame('Compte non activé', $rows[1]->getLabel());
        $this->assertSame('Candidature en dérogation', $rows[2]->getLabel());
        $this->assertSame('Candidature initialisée - contrôle éligibilité', $rows[3]->getLabel());
        $this->assertSame('Candidature initialisée', $rows[4]->getLabel());
        $this->assertSame('Candidature en cours', $rows[5]->getLabel());
        $this->assertSame('Candidature validée candidat', $rows[6]->getLabel());
        $this->assertSame('Candidature approuvée', $rows[7]->getLabel());
        $this->assertSame('Candidature refusée', $rows[8]->getLabel());
        $this->assertSame('Candidature annulée', $rows[9]->getLabel());
        $this->assertSame('Démission', $rows[10]->getLabel());

        $this->assertSame(14, $rows[0]->getValues()[0]);
        $this->assertSame(14, $rows[0]->getValues()[1]);
        $this->assertSame(2, $rows[1]->getValues()[0]);
        $this->assertSame(2, $rows[1]->getValues()[1]);
        $this->assertSame(3, $rows[2]->getValues()[0]);
        $this->assertSame(3, $rows[2]->getValues()[1]);
        $this->assertSame(0, $rows[3]->getValues()[0]);
        $this->assertSame(0, $rows[3]->getValues()[1]);
        $this->assertSame(6, $rows[4]->getValues()[0]);
        $this->assertSame(6, $rows[4]->getValues()[1]);
        $this->assertSame(0, $rows[5]->getValues()[0]);
        $this->assertSame(0, $rows[5]->getValues()[1]);
        $this->assertSame(0, $rows[6]->getValues()[0]);
        $this->assertSame(0, $rows[6]->getValues()[1]);
        $this->assertSame(0, $rows[7]->getValues()[0]);
        $this->assertSame(0, $rows[7]->getValues()[1]);
        $this->assertSame(3, $rows[8]->getValues()[0]);
        $this->assertSame(3, $rows[8]->getValues()[1]);
        $this->assertSame(0, $rows[9]->getValues()[0]);
        $this->assertSame(0, $rows[9]->getValues()[1]);
        $this->assertSame(0, $rows[10]->getValues()[0]);
        $this->assertSame(0, $rows[10]->getValues()[1]);
    }
}