<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Student;

use App\Service\Utils;
use App\Constants\Errors\ErrorsConstants;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnAuthenticationFailure implements EventSubscriberInterface
{
    public function __construct(
        private Utils $utils
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_FAILURE => 'onAuthenticationFailureEvent'
        ];
    }

    public function onAuthenticationFailureEvent(AuthenticationFailureEvent $event): void
    {
        /** @var JWTAuthenticationFailureResponse $response */
        $response = $event->getResponse();
        $response->setMessage($this->utils->getMessageByKey(ErrorsConstants::ERROR_CANDIDATE_CONNEXION_FORM));
        $event->setResponse($response);
    }
}
