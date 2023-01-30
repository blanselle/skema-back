<?php

namespace App\Twig;

use App\Entity\User;
use App\Repository\UserRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UserExtension extends AbstractExtension
{
    public function __construct(private UserRepository $userRepository) {}

    public function getFilters(): array
    {
        return [
            new TwigFilter('getUser', [$this, 'getUser']),
        ];
    }

    public function getUser(?string $username): string
    {
        if (null === $username) {
            return '';
        }

        /** @var User|null $user */
        $user = $this->userRepository->loadUserByIdentifier($username);

        return (null !== $user)? (string) $user : $username;
    }
}