<?php

declare(strict_types=1);

namespace App\Tests\Functional\api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

abstract class AbstractAuthenticatedTest extends ApiTestCase
{

    protected Client $client;
    protected EntityManagerInterface $em;

    use RefreshDatabaseTrait;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    protected function createClientWithCredentials($token = null): Client
    {
        $token = $token ?: $this->getToken();

        $this->client->setDefaultOptions(['headers' => ['authorization' => 'Bearer '.$token]]);

        return $this->client;
    }

    /**
     * Use other credentials if needed.
     */
    protected function getToken($body = []): string
    {
        $response = $this->client->request('POST', '/api/authentication_token', [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => $body ?: [
                'email' => 'admin@skema.fr',
                'password' => 'mdp',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $data = json_decode($response->getContent());

        $this->token = $data->token;

        return $data->token;
    }

    protected function rebootKernel(): void
    {
        // Reboot kernel manually
        $this->client->getKernel()->shutdown();
        $this->client->getKernel()->boot();
        // Prevent client from rebooting the kernel
        $this->client->disableReboot();

        $this->em = $this->client->getContainer()->get(EntityManagerInterface::class);
    }
}