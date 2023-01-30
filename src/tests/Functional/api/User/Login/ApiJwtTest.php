<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\User\Login;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class ApiJwtTest extends ApiTestCase
{
    public function testJwtOk(): void
    {
        static::createClient()->request('POST', '/api/authentication_token',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => 'candidate.ast1@skema.fr',
                    'password' => 'mdp',
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    public function testJwtKo(): void
    {
        static::createClient()->request('POST', '/api/authentication_token',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'email' => 'candidate.ast1@skema.fr',
                    'password' => 'error',
            ],
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}