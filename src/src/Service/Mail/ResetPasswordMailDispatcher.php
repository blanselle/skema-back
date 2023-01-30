<?php

declare(strict_types=1);

namespace App\Service\Mail;

use App\Repository\UserRepository;
use App\Service\Bloc\BlocRewriter;
use App\Service\User\TokenManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class ResetPasswordMailDispatcher
{
    public function __construct(
        private MailerEngine $mailer,
        private UserRepository $userRepository,
        private BlocRewriter $blocRewriter,
        private ParameterBagInterface $params,
        private TokenManager $tokenManager,
    ) {
    }

    public function dispatch(string $email): void
    {
        $user = $this->userRepository->findOneByEmail($email);

        if (null === $user) {
            throw new UserNotFoundException("The email does not exist");
        }

        $token = $this->tokenManager->create($user);
        $link = strval($this->params->get('reset_password_url')) . "?token={$token}";

        $bloc = $this->blocRewriter->rewriteBloc(
            bloc: 'MAIL_RESET_PASSWORD',
            params: [
                'email' => $user->getEmail(),
                'link' => $link,
            ],
        );

        $this->mailer->dispatch([$user->getEmail()], $bloc->getLabel(), $bloc->getContent());
    }
}
