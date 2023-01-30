<?php

declare(strict_types=1);

namespace App\Tests\Functional\api\ResetPassword;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response as TestResponse;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\User\TokenManager;
use App\Service\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordControllerTest extends ApiTestCase
{
    private const PASSWORD_EXAMPLE = 'mdp';
    private const USER_EMAIL_EXAMPLE = 'user0@mail.fr';

    private TokenManager $tokenManager;
    private EntityManagerInterface $em;
    private UserRepository $userRepository;
    private Utils $utils;

    protected function setUp(): void
    {
        $this->tokenManager = $this->getContainer()->get(TokenManager::class);
        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = $this->em->getRepository(User::class);
        $this->utils = $this->getContainer()->get(Utils::class);
    }

    public function testResetPasswordWithoutPassword()
    {
        $this->removeUser(self::USER_EMAIL_EXAMPLE);
        $user = (new User())
            ->setEmail(self::USER_EMAIL_EXAMPLE)
            ->setLastName('test')
            ->setFirstName('test')
            ->setRoles(['ROLE_ADMIN'])
            ->setPlainPassword(self::PASSWORD_EXAMPLE)
        ;

        $this->em->persist($user);
        $this->em->flush();

        $response = $this->resetPasswordRequest([
            'token' => $this->tokenManager->create($user),
        ]);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertStringContainsString('Password missing', $content->message);
    }

    public function testResetPasswordWithoutToken()
    {
        $this->removeUser(self::USER_EMAIL_EXAMPLE);
        $user = (new User())
            ->setEmail(self::USER_EMAIL_EXAMPLE)
            ->setLastName('test')
            ->setFirstName('test')
            ->setRoles(['ROLE_ADMIN'])
            ->setPlainPassword(self::PASSWORD_EXAMPLE)
        ;

        $this->em->persist($user);
        $this->em->flush();

        $response = $this->resetPasswordRequest([
            'password' => self::PASSWORD_EXAMPLE,
        ]);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertStringContainsString('Token missing', $content->message);
    }

    public function testResetPasswordBadToken()
    {
        $this->removeUser(self::USER_EMAIL_EXAMPLE);
        $user = (new User())
            ->setEmail(self::USER_EMAIL_EXAMPLE)
            ->setLastName('test')
            ->setFirstName('test')
            ->setRoles(['ROLE_ADMIN'])
            ->setPlainPassword('Motdepasse123!')
        ;

        $this->em->persist($user);
        $this->em->flush();

        $response = $this->resetPasswordRequest();

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertStringContainsString($this->utils->getMessageByKey('ERROR_REINIT_PASSWORD'), $content->message);
    }

    public function testResetPasswordOk()
    {
        $this->removeUser(self::USER_EMAIL_EXAMPLE);
        $user = (new User())
            ->setEmail(self::USER_EMAIL_EXAMPLE)
            ->setLastName('test')
            ->setFirstName('test')
            ->setRoles(['ROLE_ADMIN'])
            ->setPlainPassword(self::PASSWORD_EXAMPLE . '_old')
        ;

        $this->em->persist($user);
        $this->em->flush();

        $response = $this->resetPasswordRequest([
            'token' => $this->tokenManager->create($user),
            'password' => self::PASSWORD_EXAMPLE
        ]);

        
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertStringContainsString($this->utils->getMessageByKey('MSG_REINIT_PASSWORD_SUCCESS'), $content->message);

        // Check new password
        $user = $this->userRepository->findOneByEmail($user->getEmail());
        $passwordHasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($user, self::PASSWORD_EXAMPLE));
    }
    
    private function resetPasswordRequest($params = [
        'token' => 'invalid-token', 
        'password' => self::PASSWORD_EXAMPLE
    ]): TestResponse
    {
        return static::createClient()->request('POST', '/api/reset-password/reset', [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => $params,        
        ]);
    }

    private function removeUser(string $email): void
    {
        $user = $this->userRepository->findOneByEmail($email);
        if(null === $user) return;
        $this->em->remove($user);
        $this->em->flush();
    }
}