<?php

namespace App\Tests\Unit\Helper;

use App\Entity\OralTest\SlotConfiguration;
use App\Entity\OralTest\SlotType;
use App\Entity\OralTest\TestConfiguration;
use App\Entity\OralTest\TestType;
use App\Enum\SlotDetailBreakPoint;
use App\Helper\SlotDetailHelper;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SlotDetailHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCompute()
    {


        $this->assertEquals([
            $this->provideSlotDetail("1970-01-01 09:40:00.000000", "1970-01-01 10:00:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 10:05:00.000000", "1970-01-01 10:25:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 10:30:00.000000", "1970-01-01 10:50:00.000000", SlotDetailBreakPoint::LAST_SLOT_BEFORE_MORNING_BREAK),
            $this->provideSlotDetail("1970-01-01 11:05:00.000000", "1970-01-01 11:25:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 11:30:00.000000", "1970-01-01 11:50:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 11:55:00.000000", "1970-01-01 12:15:00.000000", SlotDetailBreakPoint::LAST_SLOT_ON_MORNING),
            $this->provideSlotDetail("1970-01-01 14:05:00.000000", "1970-01-01 14:25:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 14:30:00.000000", "1970-01-01 14:50:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 14:55:00.000000", "1970-01-01 15:15:00.000000", SlotDetailBreakPoint::LAST_SLOT_BEFORE_AFTERNOON_BREAK),
            $this->provideSlotDetail("1970-01-01 15:30:00.000000", "1970-01-01 15:50:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 15:55:00.000000", "1970-01-01 16:15:00.000000", SlotDetailBreakPoint::NONE_PRIORITY),
            $this->provideSlotDetail("1970-01-01 16:20:00.000000", "1970-01-01 16:40:00.000000", SlotDetailBreakPoint::LAST_SLOT_ON_AFTERNOON),
        ],
            SlotDetailHelper::compute($this->provideTestConfiguration(), 5)
        );
    }

    private function provideTestConfiguration(): TestConfiguration
    {
        return (new TestConfiguration())
            ->setTestType((new TestType())->setCode('ent'))
            ->setDurationOfTest(20)
            ->addSlotConfiguration(
                (new SlotConfiguration())
                ->setSlotType((new SlotType())->setCode('M'))
                ->setStartTime(new DateTimeImmutable('1970-1-1 09:40:00'))
                ->setEndTime(new DateTimeImmutable('1970-1-1 12:15:00'))
                ->setBreakTime(new DateTimeImmutable('1970-1-1 10:55:00'))
                ->setBreakDuration(10)
            )
            ->addSlotConfiguration(
                (new SlotConfiguration())
                    ->setSlotType((new SlotType())->setCode('A'))
                    ->setStartTime(new DateTimeImmutable('1970-1-1 14:05:00'))
                    ->setEndTime(new DateTimeImmutable('1970-1-1 16:45:00'))
                    ->setBreakTime(new DateTimeImmutable('1970-1-1 15:20:00'))
                    ->setBreakDuration(10)
            )
        ;
    }

    private function provideSlotDetail(string $start, string $end, SlotDetailBreakPoint $point): array
    {
        return ['start_time' => new DateTimeImmutable($start), 'end_time' => new DateTimeImmutable($end), 'break_point' => $point];
    }
}