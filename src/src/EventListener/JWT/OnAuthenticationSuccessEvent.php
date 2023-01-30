<?php

declare(strict_types=1);

namespace App\EventListener\JWT;

use App\Constants\User\UserRoleConstants;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OnAuthenticationSuccessEvent
{
    public function onAuthenticationSuccessEvent(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        if (!in_array(UserRoleConstants::ROLE_CANDIDATE, $user->getRoles(), true)) {
            throw new AccessDeniedException('Only candidate is allowed to login via API');
        }
    }
}
