<?php

declare(strict_types=1);

namespace App\Tests\Unit\Helper;

use App\Helper\DateFormatterHelper;
use DateTime;
use Monolog\Test\TestCase;

class DateFormatterHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testFormatFullIsOk(): void
    {
        $date = DateTime::createFromFormat('Y-m-d', '2018-12-01');

        $result = DateFormatterHelper::formatFull($date);

        $this->assertSame('Samedi 01 décembre 2018', $result);
    }
}