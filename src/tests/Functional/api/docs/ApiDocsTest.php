<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\docs;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ApiDocsTest extends ApiTestCase
{
    public function testApiDocs(): void
    {
        static::createClient()->request('GET', '/api/docs');
        $this->assertResponseIsSuccessful();
    }
}