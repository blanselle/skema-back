<?php

namespace App\Service\Workflow;

use App\Constants\Bloc\BlocConstants;
use App\Constants\Notification\NotificationConstants;
use App\Constants\ProgramChannel\ProgramChannelKeyConstants;
use App\Entity\CV\BacSup;
use App\Entity\Diploma\Diploma;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Manager\NotificationManager;
use App\Service\Notification\NotificationCenter;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

class ProgramChannelSwitchManager
{
    public const ERROR_LOGIC_DIPLOMA = "La voie de concours sélectionnée n’est pas possible pour le diplôme %s";

    public function __construct(
        private EntityManagerInterface $em,
        private NotificationManager $notificationManager,
        private NotificationCenter $notificationCenter
    ) {}

    public function updateProgramChannelVerification(Student $student, ProgramChannel $newProgramChannel): void
    {
        $diplomasVerification = $this->isDiplomasCoherentWithProgramChannel($student, $newProgramChannel);

        if (!($diplomasVerification['result']?? false)) {
            throw new LogicException(sprintf(self::ERROR_LOGIC_DIPLOMA, $diplomasVerification['error']->getDiploma()->getName()));
        }

        if ($student->getCv() !== null && $student->getCv()->getValidated()) {
            $bacSupsVerification = $this->isBacSupsCoherentWithProgramChannel($student, $newProgramChannel);
            if (!($bacSupsVerification['result']?? false)) {
                throw new LogicException(sprintf(self::ERROR_LOGIC_DIPLOMA, $bacSupsVerification['error']));
            }
            if (!$this->numberOfBacSupsCoherentWithProgramChannel($student, $newProgramChannel)) {
                throw new LogicException("Vérifier les années post bac pour pouvoir modifier la voie de concours");
            }
            if (!$this->hasSchoolReport($student, $newProgramChannel)) {
                throw new LogicException("Vérifier les moyennes post bac pour pouvoir modifier la voie de concours");
            }


        }
    }

    public function dispatch(Student $student, ProgramChannel $newProgramChannel): void
    {
        $notification = $this->notificationManager->createNotification(
            receiver: $student->getUser(),
            blocKey: BlocConstants::BLOC_PROGRAM_CHANNEL_SWITCHED,
            params: ['default.libelle_voie' => $newProgramChannel->getName()],
        );

        $this->notificationCenter->dispatch($notification, [NotificationConstants::TRANSPORT_EMAIL, NotificationConstants::TRANSPORT_DB]);
    }

    private function isDiplomasCoherentWithProgramChannel(Student $student, ProgramChannel $newProgramChannel): array
    {
        $diplomas = $this->em->getRepository(Diploma::class)->getDiplomasByProgramChannel($newProgramChannel->getId());
        /** @var StudentDiploma $studentDiploma */
        foreach ($student->getAdministrativeRecord()->getStudentDiplomas() as $studentDiploma) {
            $coherent = false;
            /** @var Diploma $diplomaPossibility */
            foreach ($diplomas as $diplomaPossibility) {
                if ($diplomaPossibility == $studentDiploma->getDiploma()) {
                    $coherent = true;
                    break;
                }
            }
            if (!$coherent) {
                return ['result' => false, 'error' => $studentDiploma];
            }
        }

        return ['result' => true];
    }

    private function isBacSupsCoherentWithProgramChannel(Student $student, ProgramChannel $newProgramChannel): array
    {
        $bacSups = $this->em->getRepository(BacSup::class)->getBacSupsWithProgramChannel($newProgramChannel->getId());
        $studentPrincipalBacSups = $this->getPrincipalBacSups($student->getCv()->getBacSups());
        foreach ($studentPrincipalBacSups as $bacSup) {
            $coherent = false;
            /** @var BacSup $bacSupPossibility */
            foreach ($bacSups as $bacSupPossibility) {
                if ($bacSupPossibility == $bacSup) {
                    $coherent = true;
                    break;
                }
            }
            if (!$coherent) {
                return ['result' => false, 'error' => $bacSup];
            }
        }

        return ['result' => true];
    }

    private function numberOfBacSupsCoherentWithProgramChannel(Student$student, ProgramChannel $newProgramChannel): bool
    {
        if ($newProgramChannel->getKey() != ProgramChannelKeyConstants::AST1 && $newProgramChannel->getKey() != ProgramChannelKeyConstants::AST2) {
            return true;
        }

        $studentPrincipalBacSups = $this->getPrincipalBacSups($student->getCv()->getBacSups());
        if ($newProgramChannel->getKey() === ProgramChannelKeyConstants::AST1 && $studentPrincipalBacSups->count() >= 1 ||
            $newProgramChannel->getKey() === ProgramChannelKeyConstants::AST2 && $studentPrincipalBacSups->count() >= 2
        ) {
            return true;
        }

        return false;
    }

    private function hasSchoolReport(Student $student, ProgramChannel $newProgramChannel): bool
    {
        if ($newProgramChannel->getKey() != ProgramChannelKeyConstants::AST1 && $newProgramChannel->getKey() != ProgramChannelKeyConstants::AST2) {
            return true;
        }

        $studentPrincipalBacSups = $this->getPrincipalBacSups($student->getCv()->getBacSups());
        /** @var BacSup $bacSup */
        foreach ($studentPrincipalBacSups as $bacSup) {
            if (0 === $bacSup->getSchoolReports()->count()) {
                return false;
            }
        }

        return true;
    }

    private function getPrincipalBacSups(Collection $bacSups): Collection
    {
        return $bacSups->filter(fn(BacSup $bacSup) => $bacSup->getParent() === null);
    }
}