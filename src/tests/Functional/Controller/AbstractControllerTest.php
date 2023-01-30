<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Constants\User\UserRoleConstants;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected const USER_EMAIL_EXAMPLE = 'user@email.fr';

    protected EntityManagerInterface $em;
    protected UserRepository $userRepository;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->em = $this->getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = $this->getContainer()->get(UserRepository::class);

    }
    
    protected function userProvider(string $email = self::USER_EMAIL_EXAMPLE, array $roles = [UserRoleConstants::ROLE_ADMIN]): User
    {
        $this->removeUser($email); // On s'assure que l'utilisateur n'existe pas déjà
        
        $user = (new User())
            ->setEmail($email)
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setRoles($roles)
            ->setPlainPassword('mdp')
        ;

        $this->em->persist($user);
        $this->em->flush();

        $user = $this->userRepository->findOneByEmail($email);

        return $user;
    }

    protected function login(User $user = null): void
    {
        if(null !== $user) {
            $this->client->loginUser($user, 'back_office');
        }
    }

    public function loginAsAdmin(): void
    {
        $this->login($this->userProvider(roles: [UserRoleConstants::ROLE_ADMIN]));
    }

    protected function removeUser(string $email): void
    {
        $user = $this->userRepository->findOneByEmail($email);
        if(null === $user) return;
        $this->em->remove($user);
        $this->em->flush();
    }
}