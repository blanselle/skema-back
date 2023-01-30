<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Dashboard;

use App\Entity\ProgramChannel;
use App\Repository\MediaRepository;
use App\Service\Dashboard\DashboardMediaToValidateCounter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardMediaToValidateCounterTest extends TestCase
{
    private MediaRepository|MockObject $mediaRepository;

    private DashboardMediaToValidateCounter $counter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mediaRepository = $this->createMock(MediaRepository::class);

        $this->counter = new DashboardMediaToValidateCounter(
            $this->mediaRepository,
        );
    }

    public function testGetRowsIsOk(): void
    {
        $this->mediaRepository
            ->expects($this->exactly(2))
            ->method('findNbStudentWithMediaToValidate')
            ->willReturn([
                [
                    'code' => 'CROUS',
                    'count' => 3,
                ],
                [
                    'code' => 'BULLETIN',
                    'count' => 2,
                ],
            ], [
                [
                    'code' => 'CROUS',
                    'count' => 0,
                ],
                [
                    'code' => 'BULLETIN',
                    'count' => 1,
                ],
            ]);
        ;

        $rows = $this->counter->getRows([new ProgramChannel(), new ProgramChannel()]);
        
        foreach($rows as $row) {
            $this->assertCount(2, $row->getValues());
        }
        
        $this->assertSame('Attestation de bourses', $rows['CROUS']->getLabel());
        $this->assertSame('Relevé de notes', $rows['BULLETIN']->getLabel());
        
    }
}