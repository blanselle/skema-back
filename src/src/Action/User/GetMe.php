<?php

declare(strict_types=1);

namespace App\Action\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class GetMe extends AbstractController
{
    public function __invoke(): User
    {
        /** @var User $user */
        $user = $this->getUser();

        return $user;
    }
}
