<?php

namespace App\Tests\Unit\Helper;

use App\Entity\CV\Bac\Bac;
use App\Helper\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCamelCaseToSnakeCase(): void
    {
        $this->assertEquals('camel_case', StringHelper::camelCaseToSnakeCase('camelCase'));
    }

    public function testGetClassName(): void
    {
        $this->assertEquals('Bac', StringHelper::getClassName(Bac::class));
    }
}