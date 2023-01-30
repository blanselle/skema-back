<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Student;

use App\Entity\Student;
use App\Entity\User\StudentWorkflowHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class WorkflowHistorySubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.candidate.entered' => 'historyze',
        ];
    }

    public function historyze(Event $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        $studentWorkflowHistory = new StudentWorkflowHistory();
        $studentWorkflowHistory->setStudent($student);
        $studentWorkflowHistory->setState($student->getState());
        $studentWorkflowHistory->setTransition($student->getTransition());

        $this->em->persist($studentWorkflowHistory);
        $this->em->flush();
    }
}
