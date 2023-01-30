<?php

declare(strict_types=1);

namespace App\EventSubscriber\User;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Constants\Errors\ErrorsConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Entity\User;
use App\Service\Utils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class PostExemptedUser implements EventSubscriberInterface
{
    public function __construct(private Utils $utils) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['isExempted', EventPriorities::POST_WRITE],
            ],
        ];
    }

    public function isExempted(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (
            $entity instanceof User
            && null !== $entity->getStudent()
            && StudentWorkflowStateConstants::STATE_EXEMPTION === $entity->getStudent()->getState()
            && Request::METHOD_POST === $method
        ) {
            throw new AccessDeniedHttpException(
                $this->utils->getMessageByKey(ErrorsConstants::ERROR_CANDIDATE_EXEMPTION)
            );
        }
    }
}
