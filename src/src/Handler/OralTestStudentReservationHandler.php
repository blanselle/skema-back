<?php

declare(strict_types=1);

namespace App\Handler;

use App\Entity\OralTest\OralTestStudent;
use App\Message\OralTestStudentReservation;
use App\Service\Workflow\OralTest\OralTestStudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class OralTestStudentReservationHandler implements MessageHandlerInterface
{
    public function __construct(
        private OralTestStudentWorkflowManager $oralTestStudentWorkflowManager,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(OralTestStudentReservation $oralTestStudentReservation): void
    {
        /** @var OralTestStudent $oralTestStudent */
        $oralTestStudent = $this->em->getRepository(OralTestStudent::class)
            ->find($oralTestStudentReservation->getOralTestStudentId());

        if (null === $oralTestStudent) {
            return;
        }

        $this->oralTestStudentWorkflowManager->reject($oralTestStudent);
        $this->oralTestStudentWorkflowManager->validate($oralTestStudent);

        $this->em->flush();
    }
}
