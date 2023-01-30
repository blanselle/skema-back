<?php

declare(strict_types=1);

namespace App\EventSubscriber\Datatable;

use App\Entity\Student;
use App\Event\Datatable\DatatableCreateQueryEvent;
use App\Repository\StudentRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DatatableCreateQuerySubscriber implements EventSubscriberInterface
{
    public function __construct(private StudentRepository $studentRepository) {}

    public static function getSubscribedEvents(): array
    {
        return [
            DatatableCreateQueryEvent::NAME => 'queryCreated',
        ];
    }

    public function queryCreated(DatatableCreateQueryEvent $event): void
    {
        if ($event->getEntityName() !== Student::class) {
            return;
        }

        $media = $event->getRequest()->query->get('media');
        $mediaCode = $event->getRequest()->query->get('mediaCode');
        $intern = $event->getRequest()->query->getBoolean('intern', true);
        $externalSession = $event->getRequest()->query->getBoolean('externalSession');

        $qb = $this->studentRepository->addFilterToSearchStudents(
            qb: $event->getQueryBuilder(),
            alias: 'a',
            intern: $intern,
            media: $media,
            mediaCode: $mediaCode,
            externalSession: $externalSession
        );
    }
}
