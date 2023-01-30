<?php

declare(strict_types=1);

namespace App\Tests\Functional\api;

class SummonsControllerTest extends AbstractAuthenticatedTest
{
    public function testSummonsIsOk()
    {
        $token = $this->getToken([
            'email' => 'candidate.ast1@skema.fr',
            'password' => 'mdp',
        ]);

        $response = $this->createClientWithCredentials($token)->request('GET', '/api/students/summons');

        $this->assertResponseIsSuccessful();

        $response = json_decode($response->getContent(), true);
    }
}
