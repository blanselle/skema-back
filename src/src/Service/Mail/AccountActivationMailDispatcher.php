<?php

declare(strict_types=1);

namespace App\Service\Mail;

use App\Entity\Bloc\Bloc;
use App\Entity\Student;
use App\Exception\Bloc\BlocNotFoundException;
use App\Service\Bloc\BlocRewriter;
use App\Service\User\TokenManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AccountActivationMailDispatcher
{
    private const TOKEN_EXPIRATION = '3 month';

    public function __construct(
        private MailerEngine $mailer,
        private BlocRewriter $blocRewriter,
        private ParameterBagInterface $params,
        private TokenManager $tokenManager,
        private LoggerInterface $logger,
    ) {
        $this->tokenManager->setEndDateExpiration(self::TOKEN_EXPIRATION);
    }

    public function dispatch(Student $student): void
    {
        $token = $this->tokenManager->create($student->getUser());
        $link = strval($this->params->get('account_activation_url')) . "?token={$token}";
        
        try {
            $bloc = $this->blocRewriter->rewriteBloc(
                bloc: 'ACCOUNT_ACTIVATION_MAIL',
                params: [
                    'firstname' => $student->getUser()->getFirstName(),
                    'link' => $link,
                ],
            );
        } catch (BlocNotFoundException $e) {
            $this->logger->critical('The account activation mail did not send : ' . $e->getMessage(), [
                'userIdentifier' => $student->getUser()->getUserIdentifier(),
            ]);

            $bloc = (new Bloc())
                ->setLabel("Activer votre compte")
                ->setContent("<p>Bonjour " . $student->getUser()->getFirstName() . ",<br /> <br />Cliquer <a href='" . $link . "'>ici</a> pour activer votre compte sur le site concours Skema et compléter votre dossier de candidature. <br /><br />Le service concours")
            ;
        }
        
        $this->mailer->dispatch([$student->getUser()->getEmail()], $bloc->getLabel(), $bloc->getContent());
    }
}
