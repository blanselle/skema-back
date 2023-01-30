<?php

declare(strict_types=1);

namespace App\EventSubscriber\Student;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AutomaticStudentOnPostInterface;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class SetStudentOnPost implements EventSubscriberInterface
{
    public function __construct(private Security $security) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['setStudent', EventPriorities::PRE_VALIDATE],
            ],
        ];
    }

    public function setStudent(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$entity instanceof AutomaticStudentOnPostInterface) {
            return;
        }

        if (Request::METHOD_POST !== $method) {
            return;
        }

        if (null === $this->security->getUser()) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        if (null === $user->getStudent()) {
            return;
        }

        $entity->setStudent($user->getStudent());
    }
}
