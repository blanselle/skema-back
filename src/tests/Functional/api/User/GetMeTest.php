<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\User;

use App\Tests\Functional\api\AbstractAuthenticatedTest;
use Symfony\Component\HttpFoundation\Response;

class GetMeTest extends AbstractAuthenticatedTest
{
    public function testGetMeOK()
    {
        $token = $this->getToken([
            'email' => 'candidate.ast1@skema.fr',
            'password' => 'mdp',
        ]);

        $response = $this->createClientWithCredentials($token)->request('GET', '/api/users/me');

        $this->assertResponseIsSuccessful();

        $response = json_decode($response->getContent(), true);
        $this->assertEquals($response['email'], 'candidate.ast1@skema.fr');
        $this->assertNotNull($response['student']['simplifiedStatus']);
    }

    public function testGetMeKO()
    {
        $this->createClientWithCredentials('test')->request('GET', '/api/users/me');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}