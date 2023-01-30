<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Cv;

use App\Constants\CV\TagBacConstants;
use App\Service\Cv\GetTypeBacFromYear;
use PHPUnit\Framework\TestCase;

class GetTypeBacFromYearTest extends TestCase
{
    private GetTypeBacFromYear $getTypebacFromYear;

    protected function setUp(): void
    {
        parent::setUp();

        $this->getTypebacFromYear = $this->getTypebacFromYear = new GetTypeBacFromYear();
    }

    public function testGetInferiorGetTag1(): void
    {
        $this->assertEquals(TagBacConstants::V1, $this->getTypebacFromYear->get(2000));
    }

    public function testGetInferiorGetTag2(): void
    {
        $this->assertEquals(TagBacConstants::V2, $this->getTypebacFromYear->get(2050));
    }
}