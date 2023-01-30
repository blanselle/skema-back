<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Exception\Bloc\BlocNotFoundException;
use App\Repository\BlocRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class UserEmailAlreadyExists
{
    public function __construct(
        private UserRepository $userRepository,
        private BlocRepository $blocRepository,
    ) {
        
    }
    public function check(User $user): void
    {
        /** @var User $user */

        if($this->userRepository->emailExist($user->getEmail())) {
            
            $bloc = $this->blocRepository->findActiveByKey('MESSAGE_EMAIL_ALREADY_EXISTS');
            
            if (null === $bloc) {
                throw new BlocNotFoundException('MESSAGE_EMAIL_ALREADY_EXISTS');
            }

            throw new BadRequestException(str_replace('%email%', $user->getEmail(), $bloc->getContent()));
        }
    }
}
