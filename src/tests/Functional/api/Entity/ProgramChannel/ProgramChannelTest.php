<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity\ProgramChannel;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ProgramChannelTest extends ApiTestCase
{
    public function testProgramsListeOk(): void
    {
        static::createClient()->request('GET', '/api/program_channels');
        $this->assertResponseIsSuccessful();
    }

    public function testProgramsItemOk(): void
    {
        static::createClient()->request('GET', '/api/program_channels/7');
        $this->assertResponseIsSuccessful();
    }
}