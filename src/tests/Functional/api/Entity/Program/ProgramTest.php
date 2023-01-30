<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity\Program;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ProgramTest extends ApiTestCase
{
    public function testProgramsListeOk(): void
    {
        static::createClient()->request('GET', '/api/programs');
        $this->assertResponseIsSuccessful();
    }

    public function testProgramsItemOk(): void
    {
        static::createClient()->request('GET', '/api/programs/6');
        $this->assertResponseIsSuccessful();
    }
}