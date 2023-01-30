<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\Entity;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class CampusTest extends ApiTestCase
{
    public function testGetCollection(): void
    {
        static::createClient()->request('GET', '/api/campuses');
        $this->assertResponseIsSuccessful();
    }

    public function testGetCampusOk(): void
    {
        $result = static::createClient()->request('GET', '/api/campuses');
        $items = json_decode($result->getContent(), true);
        static::createClient()->request('GET', $items['hydra:member'][0]['@id']);
        $this->assertResponseIsSuccessful();
    }
}