<?php

declare(strict_types=1);

namespace App\DataFixtures\Providers;

use App\Constants\User\UserRoleConstants;

class UserRoleProvider
{
    public function randomRoles(): array
    {
        return [array_rand(UserRoleConstants::getConsts())];
    }
}
