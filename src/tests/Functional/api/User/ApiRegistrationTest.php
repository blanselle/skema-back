<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\User;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Country;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\DiplomaChannel;
use App\Entity\ProgramChannel;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiRegistrationTest extends ApiTestCase
{
    private const TEST_EMAIL = 'test.registration.candidate.ast1@skema.fr';
    private const TEST_PASSWORD = 'Azertyuiop123!';

    private EntityManagerInterface $em;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
    }

    public function testRegistrationWithWrongformattedPasswordGetAnError()
    {
        $this->removeUser();
        $response = $this->registration($this->provideUserData(password: 'mdp'));

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertNull($this->em->getRepository(User::class)->findOneByEmail(self::TEST_EMAIL));
    }

    public function testRegistrationIsOk(): void
    {
        $this->removeUser();
        $response = $this->registration();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_CREATED);
        $this->assertNotNull($this->em->getRepository(User::class)->findOneByEmail(self::TEST_EMAIL));
    }

    public function testRegistrationSameEmailGetAnError(): void
    {
        $this->removeUser();
        $response = $this->registration();
        
        $response = $this->registration();
        
        $this->assertEquals($response->getStatusCode(), Response::HTTP_BAD_REQUEST);
    }

    public function testRegistrationSameEmailUpperCaseGetAnError(): void
    {
        $this->removeUser();
        $response = $this->registration();
        $response = $this->registration($this->provideUserData(email: strtoupper(self::TEST_EMAIL)));

        $this->assertEquals($response->getStatusCode(), Response::HTTP_BAD_REQUEST);
    }
    
    private function removeUser(string $email = self::TEST_EMAIL): void
    {
        $user = $this->em->getRepository(User::class)->findOneByEmail($email);
        if(null !== $user) {
            $this->em->remove($user);
            $this->em->flush();
        }
    }

    private function registration(array $userData = null): ResponseInterface
    {
        if(null === $userData) {
            $userData = $this->provideUserData();
        }

        return static::createClient()->request('POST', '/api/users', [
            'headers' => [
                'accept' => 'application/json',
                'content-Type' => 'application/json',
            ],
            'json' => $userData,
        ]);
    }

    private function provideUserData(string $email = self::TEST_EMAIL, string $password = self::TEST_PASSWORD): array
    {
        $country = $this->em->getRepository(Country::class)->findOneBy(['idCountry' => 'FRA']);
        $diploma = $this->em->getRepository(Diploma::class)->findOneByName('BTS');
        $diplomaChannel = $this->em->getRepository(DiplomaChannel::class)->findOneByName('Assistant de gestion de PME-PMI');
        $programChannel = $this->em->getRepository(ProgramChannel::class)->findOneByName('AST 1');

        return [
            'email' => $email,
            'plainPassword' => $password,
            'firstName' => 'toto',
            'lastName' => 'toto',
            'student'=> [
                'dateOfBirth' => '2010-03-28T15:43:45.585Z',
                'gender' => 'F',
                'phone' => '+33672850159',
                'address' => '202 rue de la place',
                'postalCode' => '59100',
                'city' => 'Roubaix',
                'country' => '/api/countries/'.$country->getId(),
                'countryBirth' => '/api/countries/'.$country->getId(),
                'nationality' => '/api/countries/'.$country->getId(),
                'thirdTime' => false,
                'administrativeRecord' => [
                    "studentDiplomas" => [
                        [
                            "year" => 2018,
                            "diplomaChannel" => "/api/diploma_channels/" . $diplomaChannel->getId(),
                            "establishment" => "ESIEE",
                            "postalCode" => "80000",
                            "city" => "Amiens",
                            "diploma" => "/api/diplomas/" . $diploma->getId(),
                            "lastDiploma" => true,
                            "detail" => null
                        ],
                    ],
                ],
                "programChannel" => "/api/program_channels/" . $programChannel->getId(),
            ]
        ];
    }
}