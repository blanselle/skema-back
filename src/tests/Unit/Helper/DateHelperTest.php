<?php

namespace App\Tests\Unit\Helper;

use App\Helper\DateHelper;
use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetWorkingDays(): void
    {
        $this->assertEquals([
            new \DateTimeImmutable('2022-12-30'),
            new \DateTimeImmutable('2022-12-31'),
            new \DateTimeImmutable('2023-01-02'),
            new \DateTimeImmutable('2023-01-03'),
            new \DateTimeImmutable('2023-01-04'),
        ],
            DateHelper::getWorkingDays(new \DateTimeImmutable('2022-12-30'), new \DateTimeImmutable('2023-01-04'),
                ['2023-01-01'])
        );
    }

    public function testGetWorkingDaysWithOneSunday(): void
    {
        $this->assertEquals([
            new \DateTimeImmutable('2022-12-09'),
            new \DateTimeImmutable('2022-12-10'),
            new \DateTimeImmutable('2022-12-12'),
            new \DateTimeImmutable('2022-12-13'),
        ],
            DateHelper::getWorkingDays(new \DateTimeImmutable('2022-12-09'), new \DateTimeImmutable('2022-12-13'),
                ['2023-01-01'])
        );
    }

    public function testGetWorkingDaysWithoutPublicHolidays(): void
    {
        $this->assertEquals([
            new \DateTimeImmutable('2022-11-01'),
            new \DateTimeImmutable('2022-11-02'),
            new \DateTimeImmutable('2022-11-03'),
        ],
            DateHelper::getWorkingDays(new \DateTimeImmutable('2022-11-01'), new \DateTimeImmutable('2022-11-03'))
        );
    }

    public function testGetWorkDayWithoutPublicHolidays(): void
    {
        $dates = [
            new \DateTimeImmutable('2023-01-02'),
            new \DateTimeImmutable('2023-01-04'),
            new \DateTimeImmutable('2023-01-05'),
            new \DateTimeImmutable('2023-01-08'),
            new \DateTimeImmutable('2023-01-15'),
        ];
        $result = [
            new \DateTimeImmutable('2023-01-05'),
            new \DateTimeImmutable('2023-01-07'),
            new \DateTimeImmutable('2023-01-09'),
            new \DateTimeImmutable('2023-01-11'),
            new \DateTimeImmutable('2023-01-19'),
        ];

        foreach ($dates as $key => $date) {
            $this->assertEquals($result[$key], DateHelper::getWorkDay($date, 2, ['2023-01-16']));
        }
    }
}