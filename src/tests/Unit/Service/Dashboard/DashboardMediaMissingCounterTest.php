<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Dashboard;

use App\Entity\ProgramChannel;
use App\Repository\MediaRepository;
use App\Service\Dashboard\DashboardMediaMissingCounter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardMediaMissingCounterTest extends TestCase
{
    private MediaRepository|MockObject $mediaRepository;

    private DashboardMediaMissingCounter $counter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mediaRepository = $this->createMock(MediaRepository::class);

        $this->counter = new DashboardMediaMissingCounter(
            $this->mediaRepository,
        );
    }

    public function testGetRowsIsOk(): void
    {
        $this->mediaRepository
            ->expects($this->exactly(2))
            ->method('findNbStudentWithMediaMissing')
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