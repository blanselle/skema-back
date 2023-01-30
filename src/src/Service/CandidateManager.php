<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\CV\BacSupConstants;
use App\Constants\CV\DistinctionCodeConstants;
use App\Constants\Media\MediaCodeConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\Parameters\ParametersKeyConstants;
use App\Constants\ProgramChannel\ProgramChannelKeyConstants;
use App\Entity\CV\BacSup;
use App\Entity\CV\SchoolReport;
use App\Entity\Media;
use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use App\Entity\ProgramChannel;
use App\Entity\Student;
use App\Manager\StudentManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class CandidateManager
{
    private DateTime $currentDatetime;

    public function __construct(
        private EntityManagerInterface $em,
        private StudentManager $studentManager,
    ) {
        $this->currentDatetime = new DateTime();
    }

    public function isOlderThanLimit(Student $student): bool
    {
        $dateNaissanceMax = $this->em->getRepository(ParameterKey::class)->findOneBy(['name' => ParametersKeyConstants::DATE_NAISSANCE_MAX]);
        $birthdayMax = $this->em->getRepository(Parameter::class)->findOneParameterWithKeyAndProgramChannel(
            $dateNaissanceMax,
            $student->getProgramChannel()
        );

        return null !== $birthdayMax && $student->getDateOfBirth() < $birthdayMax->getValueDateTime();
    }

    public function isDateInscriptionTooEarly(ProgramChannel $programChannel): bool
    {
        $parameterInscription = $this->em->getRepository(ParameterKey::class)->findOneBy(['name' => ParametersKeyConstants::DATE_INSCRIPTION_START]);
        $dateInscription = $this->em->getRepository(Parameter::class)->findOneParameterWithKeyAndProgramChannel(
            $parameterInscription,
            $programChannel
        );

        return null !== $dateInscription && $this->currentDatetime < $dateInscription->getValueDateTime();
    }

    public function isDateInscriptionTooLate(ProgramChannel $programChannel): bool
    {
        $parameterInscription = $this->em->getRepository(ParameterKey::class)->findOneBy(['name' => ParametersKeyConstants::DATE_INSCRIPTION_END]);
        $dateInscription = $this->em->getRepository(Parameter::class)->findOneParameterWithKeyAndProgramChannel(
            $parameterInscription,
            $programChannel
        );

        return null !== $dateInscription && $this->currentDatetime > $dateInscription->getValueDateTime();
    }

    public function hasOtherDiploma(Student $student): bool
    {
        $studentLastDiploma = $this->studentManager->getStudentLastDiploma($student);
        if (null !== $studentLastDiploma && $studentLastDiploma->getDiploma()->getNeedDetail()) {
            return true;
        }

        return false;
    }

    public function isDualPath(Student $student): bool
    {
        /** @var BacSup $bacSup */
        foreach ($student->getCv()?->getBacSups() ?? [] as $bacSup) {
            if (!empty($bacSup->getDualPathBacSup())) {
                return true;
            }
        }

        return false;
    }

    public function hasDistinction(Student $student): bool
    {
        if ($student->getCv()?->getBac()?->getBacDistinction()?->getCode() !== DistinctionCodeConstants::NO_DISTINCTION) {
            return true;
        }

        return false;
    }

    public function hasAcceptedDocumentByCode(Student $student, String $code): bool
    {
        $medias = $this->em->getRepository(Media::class)->findBy(['student' => $student, 'code' => $code]);

        foreach ($medias as $media) {
            if ($media->getState() === MediaWorflowStateConstants::STATE_ACCEPTED) {
                return true;
            }
        }

        return false;
    }

    public function hasAllAcceptedDocumentsByCode(Student $student, String $code): bool
    {
        $medias = $this->em->getRepository(Media::class)->findBy(['student' => $student, 'code' => $code]);

        /** @var Media $media */
        foreach ($medias as $media) {
            if ($media->getState() === MediaWorflowStateConstants::STATE_CANCELLED) {
                continue;
            }

            if ($media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED) {
                return false;
            }
        }

        return true;
    }

    public function hasRejectedDocumentByCode(Student $student, String $code): bool
    {
        $medias = $this->em->getRepository(Media::class)->findBy(['student' => $student, 'code' => $code]);
        foreach ($medias as $media) {
            if ($media->getState() === MediaWorflowStateConstants::STATE_REJECTED) {
                return true;
            }
        }

        return false;
    }

    public function hasSchoolReportsValidated(Student $student): bool
    {
        $i = 0;
        $validated = [];
        /** @var BacSup $bacSup */
        foreach ($student->getCv()?->getBacSups() ?? [] as $bacSup) {
            if (
                $student->getProgramChannel()->getKey() === ProgramChannelKeyConstants::AST1 && $i >= 1 ||
                $student->getProgramChannel()->getKey() === ProgramChannelKeyConstants::AST2 && $i >= 2
            ) {
                break;
            }
            /** @var SchoolReport $schoolReport */
            foreach ($bacSup->getSchoolReports() as $schoolReport) {
                if ($schoolReport->getMedia()?->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED) {
                    $validated[$i]['type'] = $bacSup->getType();
                    $validated[$i]['code'][] = $schoolReport->getMedia()->getCode();
                }
            }
            $i++;
        }

        $i = 0;
        $code = "L1";
        foreach ($validated as $item) {
            if ($i == 1) {
                $code = "L2";
            }

            if (
                ($item['type'] === BacSupConstants::TYPE_ANNUAL && in_array($code, $item['code'], true)) ||
                ($item['type'] === BacSupConstants::TYPE_SEMESTRIAL && in_array($code."_S1", $item['code'], true) &&
                    in_array($code."_S2", $item['code'], true))
            ) {
                return true;
            }
            $i++;
        }

        return false;
    }

    public function hasSchoolReportsNotRejected(Student $student): bool
    {
        $i = 0;
        $validated = [];
        /** @var BacSup $bacSup */
        foreach ($student->getCv()->getBacSups() as $bacSup) {
            if (
                $student->getProgramChannel()->getKey() === ProgramChannelKeyConstants::AST1 && $i >= 1 ||
                $student->getProgramChannel()->getKey() === ProgramChannelKeyConstants::AST2 && $i >= 2
            ) {
                break;
            }
            /** @var SchoolReport $schoolReport */
            foreach ($bacSup->getSchoolReports() as $schoolReport) {
                if ($schoolReport->getMedia()?->getState() !== MediaWorflowStateConstants::STATE_REJECTED) {
                    $validated[$i]['type'] = $bacSup->getType();
                    $validated[$i]['code'][] = $schoolReport->getMedia()->getCode();
                }
            }
            $i++;
        }

        $i = 0;
        $code = "L1";
        foreach ($validated as $item) {
            if ($i == 1) {
                $code = "L2";
            }

            if (
                ($item['type'] === BacSupConstants::TYPE_ANNUAL && in_array($code, $item['code'], true)) ||
                ($item['type'] === BacSupConstants::TYPE_SEMESTRIAL && in_array($code."_S1", $item['code'], true) &&
                    in_array($code."_S2", $item['code'], true))
            ) {
                return true;
            }
            $i++;
        }

        return false;
    }

    public function hasAllDocumentsValidated(Student $student): bool
    {
        if (
            false === $this->hasAcceptedDocumentByCode($student,MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE) ||
            true === $this->isDualPath($student) && false === $this->hasAcceptedDocumentByCode($student,MediaCodeConstants::CODE_CERTIFICAT_DOUBLE_PARCOURS) ||
            false === $this->hasAcceptedDocumentByCode($student,MediaCodeConstants::CODE_ID_CARD) ||
            true === $student->getAdministrativeRecord()->getScholarShip() && false === $this->hasAcceptedDocumentByCode($student,MediaCodeConstants::CODE_CROUS) ||
            true === $student->getAdministrativeRecord()->getHighLevelSportsman() && false === $this->hasAcceptedDocumentByCode($student, MediaCodeConstants::CODE_SHN) ||
            true === $this->hasDistinction($student) && false === $this->hasAcceptedDocumentByCode($student, MediaCodeConstants::CODE_BAC) ||
            false === $this->hasSchoolReportsValidated($student)
        ) {
            return false;
        }

        return true;
    }

    public function hasAllDocumentsMandatoryToComplete(Student $student): bool
    {
        if (
            true === $this->hasRejectedDocumentByCode($student, MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE) ||
            true === $this->isDualPath($student) && true === $this->hasRejectedDocumentByCode($student,MediaCodeConstants::CODE_CERTIFICAT_DOUBLE_PARCOURS) ||
            true === $this->hasRejectedDocumentByCode($student,MediaCodeConstants::CODE_ID_CARD) ||
            true === $student->getAdministrativeRecord()->getScholarShip() && true === $this->hasRejectedDocumentByCode($student,MediaCodeConstants::CODE_CROUS) ||
            true === $student->getAdministrativeRecord()->getHighLevelSportsman() && true === $this->hasRejectedDocumentByCode($student, MediaCodeConstants::CODE_SHN) ||
            true === $this->hasDistinction($student) && true === $this->hasRejectedDocumentByCode($student, MediaCodeConstants::CODE_BAC) ||
            false === $this->hasSchoolReportsNotRejected($student)
        ) {
            return false;
        }

        return true;
    }
}
