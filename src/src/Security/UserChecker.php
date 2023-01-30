<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $authorizedRoles = ['ROLE_ADMIN', 'ROLE_COORDINATOR', 'ROLE_RESPONSABLE'];
        $authorized = false;

        foreach ($authorizedRoles as $authorizedRole) {
            if ($user->hasRole($authorizedRole)) {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
            throw new CustomUserMessageAuthenticationException(
                'Impossible de se connecter avec cet utilisateur sur le backoffice'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        $this->checkPreAuth($user);
    }
}
