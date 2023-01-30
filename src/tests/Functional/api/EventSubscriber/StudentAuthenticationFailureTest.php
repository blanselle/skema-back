<?php

declare(strict_types=1);
namespace App\Tests\Functional\api\EventSubscriber;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Constants\Errors\ErrorsConstants;
use App\Constants\Parameters\ParametersKeyConstants;
use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use App\Entity\User;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;

class StudentAuthenticationFailureTest extends ApiTestCase
{
    private EntityManagerInterface $em;
    private Utils $utils;

    protected function setUp(): void
    {
        parent::setUp();

        parent::bootKernel();

        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
        $this->utils = $this->getContainer()->get(Utils::class);
    }

    public function testKoWithStudentBadCredentials()
    {
        $parameterInscription = $this->em->getRepository(ParameterKey::class)->findOneBy(['name' => ParametersKeyConstants::DATE_INSCRIPTION_END]);

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => 'candidate.ast1@skema.fr']);

        $dateInscription = $this->em->getRepository(Parameter::class)->findOneParameterWithKeyAndProgramChannel(
            $parameterInscription,
            $user->getStudent()->getProgramChannel()
        );

        $dateInscription->setValueDateTime(new \DateTime('1945-03-15 10:00:00'));
        $this->em->persist($dateInscription);
        $this->em->flush();

        $response = static::createClient()->request('POST', '/api/authentication_token', [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => $user->getEmail(),
                'password' => 'FAIL',
            ]
        ]);
        $this->assertStringContainsString((json_decode($response->getContent(false)))->message, $this->utils->getMessageByKey(ErrorsConstants::ERROR_CANDIDATE_CONNEXION_FORM));
        $this->assertResponseStatusCodeSame(401);
    }
}