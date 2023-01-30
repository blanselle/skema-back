<?php

declare(strict_types=1);

namespace App\Tests\Functional\api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\Transport\InMemoryTransport;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class AbstractApiTest extends ApiTestCase
{
    protected Client $client;
    protected EntityManagerInterface $em;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
    }

    protected function createAst1CheckDiploma(string $email, string $password): ResponseInterface
    {
        $params = $this->provideUserPayload($email, $password);
        
        $params['student']['administrativeRecord']["studentDiplomas"] = [
            [
                "year" => 2020,
                "establishment" => "TEST",
                "postalCode" => "TESTU",
                "lastDiploma" => true,
                "diploma" => "/api/diplomas/40",
                "city" => "TETSU",
                "detail" => "string"
            ]
        ];
        
        $response = $this->request(
            method: 'POST', 
            url: '/api/users',
            body: $params,
        );
        
        $this->assertResponseIsSuccessful();

        return $response;
    }

    protected function createAst1Created(string $email, string $password): ResponseInterface
    {
        $params = $this->provideUserPayload($email, $password);        
        
        $response = $this->request(
            method: 'POST', 
            url: '/api/users',
            body: $params,
        );
        
        $this->assertResponseIsSuccessful();

        return $response;
    }
    
    protected function createAst1Exemption(string $email, string $password): ResponseInterface
    {
        $params = $this->provideUserPayload($email, $password);
        
        $params['student']["dateOfBirth"] = "1996-08-20T13:39:04+02:00";
    
        $response = $this->request(
            method: 'POST', 
            url: '/api/users',
            body: $params,
        );
    
        $this->assertResponseIsSuccessful();
        
        return $response;
    }


    protected function createAst1ExemptionAndCheckDiploma(string $email, string $password): ResponseInterface
    {
        $params = $this->provideUserPayload($email, $password);
        
        $params['student']["dateOfBirth"] = "1996-08-20T13:39:04+02:00";

        $response = $this->request(
            method: 'POST', 
            url: '/api/users',
            body: $params,
        );
    
        $this->assertResponseIsSuccessful();
        
        return $response;
    }

    protected function createMedia(string $token): ResponseInterface
    {
        $headers = ['Authorization' => "Bearer {$token}"];

        copy(__DIR__ . '/../../uploads/skema.png', '/tmp/skema.png');
        
        $uploadedFile = new UploadedFile(
            path:  '/tmp/skema.png',
            originalName: 'skema.png',
            test: false,
        );
        
        $response = $this->client->request('POST', '/api/media', [
            'headers' => $headers,
            'extra' => [
                'files' => ['formFile' => $uploadedFile],
                'parameters' => ['code' => 'certificat_eligibilite'],
            ],
        ]);

        $this->assertResponseIsSuccessful();

        return $response;
    }

    protected function activeAccount(): ResponseInterface
    {
        /** @var InMemoryTransport $transport */
        $transport = $this->getContainer()->get('messenger.transport.async_mailer');
        $this->assertNotEmpty($transport->getSent());
        $mailBody = $transport->getSent()[0]->getMessage()->getEmail()->getHtmlBody();

        $url = 'https://frontend-skema.pictime-groupe-integ.com/account-activation?token=';

        $mailBody = substr($mailBody, strpos($mailBody, $url) + strlen($url));
        $token = trim(substr($mailBody, 0, strpos($mailBody, '\'>')));

        $response = $this->request(
            method: 'POST', 
            url: '/api/students/activation',
            body: [
                'token' => $token,
            ],
        );
        
        $this->assertResponseIsSuccessful();

        return $response;
    }

    protected function getToken(string $email, string $password): string
    {
        $response = $this->request(
            method: 'POST', 
            url: '/api/authentication_token',
            body: [
                'email' => $email,
                'password' => $password,
            ],
        );
        
        $this->assertResponseIsSuccessful();

        $data = json_decode($response->getContent());

        $this->token = $data->token;

        return $data->token;
    }

    protected function request(string $method = 'GET', string $url, array $body = [], string $token = null): ResponseInterface
    {
        $headers = [
            'accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if(null !== $token) {
            $headers['Authorization'] = "Bearer {$token}";
        }

        return $this->client->request($method, $url, [
            'headers' => $headers,
            'json' => $body,
        ]);
    }

    protected function getMe(string $token): array
    {
        $response = $this->request(
            method: 'GET', 
            url: '/api/users/me',
            token: $token,
        );

        $this->assertResponseIsSuccessful();

        return json_decode($response->getContent(), true);
    }

    protected function provideUserPayload(string $email, string $password): array
    {
        return [
            "email" => $email,
            "firstName" => "test",
            "lastName" => "test",
            "plainPassword" => $password,
            "roles" => [
                "ROLE_CANDIDATE"
            ],
            "student" => [
                "dateOfBirth" => "2002-08-20T13:39:04+02:00",
                "programChannel" => "/api/program_channels/7",
                "firstNameSecondary" => "Jean, Jacques",
                "gender" => "M",
                "identifier" => "230001",
                "phone" => "+33701010101",
                "address" => "10 rue de Général de Gaule",
                "postalCode" => "59000",
                "city" => "Lille",
                "country" => "/api/countries/245",
                "countryBirth" => "/api/countries/245",
                "nationality" => "/api/countries/245",
                "thirdTime" => false,
                "administrativeRecord" => [
                    "studentDiplomas" => [[
                        "year" => 2020,
                        "establishment" => "TEST",
                        "postalCode" => "TESTU",
                        "lastDiploma" => true,
                        "diploma" => "/api/diplomas/34",
                        "diplomaChannel" => "/api/diploma_channels/128",
                        "city" => "TETSU"
                    ]]
                ] 
            ]
        ];
    }

    protected function getUser(string $email): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['email' => $email]);
    }
}
