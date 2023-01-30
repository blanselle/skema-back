<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class TokenManager
{
    protected int $expiration = 900;

    public function __construct(
        protected JWTTokenManagerInterface $jwtTokenManager,
        protected UserRepository $userRepository,
    ) {
    }

    public function create(User $user): string
    {
        $expiration = (new DateTime())->getTimestamp() + $this->expiration;

        return $this->jwtTokenManager->createFromPayload($user, [
            'exp' => $expiration,
            'userIdentifier' => $user->getId(),
        ]);
    }

    /**
     * Parse le token et retourne l'utilisateur
     * 
     * UserNotFoundException en cas d'erreur
     *
     * @param string $token
     * @return User
     */
    public function parse(string $token): User
    { 
        $payload = $this->jwtTokenManager->parse($token);       
        
        $user = $this->userRepository->findOneById($payload['userIdentifier']);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function setExpiration(int $expiration): self
    {
        if($expiration < 0) {
            throw new Exception('Expiration not valid');
        }

        $this->expiration = $expiration;

        return $this;
    }

    public function setEndDateExpiration(string $duration): self
    {
        try {
            $end = new DateTimeImmutable('+' . $duration);
        } catch(Exception $e) {
            throw new Exception('Expiration parameter not valid');
        }

        return $this->setExpiration($end->getTimestamp() - (new DateTimeImmutable())->getTimestamp());
    }
}
