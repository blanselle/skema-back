<?php

declare(strict_types=1);
namespace App\Tests\Functional\api\ResetPassword;

use App\Entity\User;
use App\Tests\Functional\api\AbstractAuthenticatedTest;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response as TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordControllerTest extends AbstractAuthenticatedTest
{
    private const USER_TEST_EMAIL = 'candidate@skema.fr';
    private const PASSWORD_EXAMPLE = 'mdp';

    private function changePasswordRequest(array $params): TestResponse
    {
        $token = $this->getToken([
            'email' => self::USER_TEST_EMAIL,
            'password' => self::PASSWORD_EXAMPLE,
        ]);

        return $this->createClientWithCredentials($token)->request('POST', '/api/reset-password/modify', [
            'headers' => [
                'accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => $params,
        ]);
    }

    public function testChangePasswordWithConfirmationPasswordDifferentToNewPassword()
    {
        $response = $this->changePasswordRequest([
            'old_password' => 'mdp',
            'new_password' => 'new_pwd',
            'confirmation_password' => 'wrong confirmation password'
        ]);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertSame('Le mot de passe de confirmation ne correspond pas au nouveau', $content->message);
    }

    public function testChangePasswordWithWrongPassword()
    {
        $response = $this->changePasswordRequest([
            'old_password' => 'wrong password',
            'new_password' => 'new_pwd',
            'confirmation_password' => 'new_pwd'
        ]);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertSame('L\'email ou le mot de passe ne correspond pas', $content->message);
    }

    public function testChangePasswordOk()
    {
        $response = $this->changePasswordRequest([
            'old_password' => self::PASSWORD_EXAMPLE,
            'new_password' => 'new_pwd',
            'confirmation_password' => 'new_pwd'
        ]);

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(false));
        $this->assertSame('Votre mot de passe a bien été modifié', $content->message);

        // Check new password
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $user = $em->getRepository(User::class)->findOneByEmail(self::USER_TEST_EMAIL);
        $passwordHasher = $this->getContainer()->get(UserPasswordHasherInterface::class);
        $this->assertTrue($passwordHasher->isPasswordValid($user, 'new_pwd'));
    }
}