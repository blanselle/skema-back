<?php

declare(strict_types=1);

namespace App\Manager;

use App\Constants\Exam\ExamSessionTypeNameConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\User\CandidacyConstants;
use App\Constants\User\SimplifiedStudentStatusConstants;
use App\Entity\Diploma\StudentDiploma;
use App\Entity\Student;
use App\Repository\MediaRepository;
use App\Service\Workflow\Student\StudentWorkflowManager;

class CandidacyManager
{
    public function __construct(private StudentWorkflowManager $studentWorkflowManager, private MediaRepository $mediaRepository) {}

    public function allValidated(Student $student): bool
    {
        if(false === $this->administrativeRecordValidated($student)) {
            return false;
        }

        if(false === $this->schoolRegistrationFeesValidated($student)) {
            return false;
        }

        if(false === $student->getCv()?->getValidated()) {
            return false;
        }

        return true;
    }

    public function administrativeRecord(Student $student): string
    {
        if($this->administrativeRecordValidatedForStudent($student)) {
            return CandidacyConstants::DONE;
        }

        return CandidacyConstants::TO_DO;
    }

    public function schoolRegistration(Student $student): string
    {
        if($this->schoolRegistrationFeesValidated($student)) {
            return CandidacyConstants::DONE;
        }

        if($this->schoolRegistrationFeesAvailable($student)) {
            return CandidacyConstants::TO_DO;
        }

        return CandidacyConstants::FORBIDDEN;
    }

    public function cv(Student $student): string
    {
        if($this->cvValidatedForStudent($student)) {
            return CandidacyConstants::DONE;
        }

        if($this->cvAvailable($student)) {
            return CandidacyConstants::TO_DO;
        }

        return CandidacyConstants::FORBIDDEN;
    }

    public function writtenExamination(Student $student): string
    {
        if($this->writtenExaminationValidated($student)) {
            return CandidacyConstants::DONE;
        }

        if($this->writtenExaminationAvailable($student)) {
            return CandidacyConstants::TO_DO;
        }

        return CandidacyConstants::FORBIDDEN;
    }

    public function hasScholarShipMedia(Student $student): bool
    {
        $scholarShipMedias = $student->getAdministrativeRecord()?->getScholarShipMedias()?? [];

        return count($scholarShipMedias) > 0;
    }

    public function hasScholarReportMedia(Student $student): bool
    {
        $bacSups = $student->getCv()?->getBacSups()?? [];
        if (count($bacSups) === 0) {
            return false;
        }

        foreach ($bacSups as $bacSup) {
            $schoolReports = $bacSup->getSchoolReports();
            foreach ($schoolReports as $schoolReport) {
                if (null === $schoolReport->getMedia()) {
                    return false;
                }
            }
        }

        return true;
    }

    public function hasScore(Student $student): bool
    {
        $examStudents = $student->getExamStudents();
        if (count($examStudents) === 0) {
            return false;
        }

        foreach($examStudents as $examStudent) {
            if (null === $examStudent->getScore()) {
                return false;
            }
        }

        return true;
    }

    private function schoolRegistrationFeesAvailable(Student $student): bool
    {
        if(!$this->studentWorkflowManager->isBeingRegistered($student)) {
            $order = $student->getOrder(type: OrderTypeConstants::SCHOOL_REGISTRATION_FEES);

            return null === $order or OrderWorkflowStateConstants::STATE_CREATED === $order->getState();
        }

        return false;
    }

    private function cvAvailable(Student $student): bool
    {
        if (in_array($student->getState(), SimplifiedStudentStatusConstants::INITIALIZED, true)) {
            return false;
        }
        if(true === $this->schoolRegistrationFeesValidated($student)) {
            return true;
        }

        return false;
    }

    private function writtenExaminationAvailable(Student $student): bool
    {
        if (in_array($student->getState(), SimplifiedStudentStatusConstants::INITIALIZED, true)) {
            return false;
        }
        if(true === $student->getAdministrativeRecord()->getScholarShip()) {
            return true;
        }

        if ($this->schoolRegistrationFeesValidated($student)) {
            return true;
        }

        return false;
    }

    /**
     * Administrative record validated for Student
     */
    public function administrativeRecordValidatedForStudent(Student $student): bool
    {
        if($this->studentWorkflowManager->isBeingRegistered($student)) {
            return false;
        }

        // media code certificat_eligibilite and certificat_double_parcours
        /** @var StudentDiploma $diploma */
        foreach ($student->getAdministrativeRecord()->getStudentDiplomas() as $diploma) {
            foreach ($diploma->getDiplomaMedias() as $media) {
                if(
                    $media->getState() === MediaWorflowStateConstants::STATE_REJECTED
                ) {
                    return false;
                }
            }
        }

        // media shn
        foreach($student->getAdministrativeRecord()->getHighLevelSportsmanMedias() as $media) {
            if(
                $media->getState() === MediaWorflowStateConstants::STATE_REJECTED
            ) {
                return false;
            }
        }

        // media code tt
        foreach($student->getAdministrativeRecord()->getThirdTimeMedias() as $media) {
            if(
                $media->getState() === MediaWorflowStateConstants::STATE_REJECTED
            ) {
                return false;
            }
        }

        // media code crous
        foreach($student->getAdministrativeRecord()->getScholarShipMedias() as $media) {
            if(
                $media->getState() === MediaWorflowStateConstants::STATE_REJECTED
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Administrative record validated for BO
     */
    private function administrativeRecordValidated(Student $student): bool
    {
        if($this->studentWorkflowManager->isBeingRegistered($student)) {
            return false;
        }

        // media code certificat_eligibilite and certificat_double_parcours
        /** @var StudentDiploma $diploma */
        foreach ($student->getAdministrativeRecord()->getStudentDiplomas() as $diploma) {
            foreach ($diploma->getDiplomaMedias() as $media) {
                if(
                    $media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED
                ) {
                    return false;
                }
            }
        }

        // media shn
        foreach($student->getAdministrativeRecord()->getHighLevelSportsmanMedias() as $media) {
            if(
                $media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED
            ) {
                return false;
            }
        }

        // media code tt
        foreach($student->getAdministrativeRecord()->getThirdTimeMedias() as $media) {
            if(
                $media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED
            ) {
                return false;
            }
        }

        // media code crous
        foreach($student->getAdministrativeRecord()->getScholarShipMedias() as $media) {
            if(
                $media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED
            ) {
                return false;
            }
        }

        return true;
    }

    private function schoolRegistrationFeesValidated(Student $student): bool
    {
        /** @todo must be removed after payment manual on candidacy sholar ship */
        if (true === $student->getAdministrativeRecord()?->getScholarShip()) {
            return true;
        }

        $order = $student->getOrder(type: OrderTypeConstants::SCHOOL_REGISTRATION_FEES);

        return OrderWorkflowStateConstants::STATE_VALIDATED === $order?->getState();
    }

    private function cvValidatedForStudent(Student $student): bool
    {
        $cv = $student->getCv();

        if(null === $cv) {
            return false;
        }

        if(false === $cv->getValidated()) {
            return false;
        }

        $bac = $cv->getBac();

        if(null === $bac) {
            return false;
        }

        if($cv->getBac()?->getMedia()?->getState() === MediaWorflowStateConstants::STATE_REJECTED) {
            return false;
        }

        foreach($cv->getBacSups() as $bacSup) {
            foreach($bacSup->getSchoolReports() as $schoolReport) {
                if($schoolReport->getMedia()?->getState() === MediaWorflowStateConstants::STATE_REJECTED) {
                    return false;
                }
            }
        }

        return true;
    }

    public function hasAllAcceptedDocumentsToCompleteCandidacy(Student $student): bool
    {
        $medias = $this->mediaRepository->fetchDocumentsToCompleteCandidacy(student: $student);
        foreach ($medias as $media) {
            if( $media->getState() !== MediaWorflowStateConstants::STATE_ACCEPTED) {
                return false;
            }
        }

        return true;
    }

    /**
     * https://pictime.atlassian.net/browse/SB-1328
     */
    public function writtenExaminationValidated(Student $student): bool
    {
        $examSessionsFlags = [];

        foreach(ExamSessionTypeNameConstants::getConsts() as $examSessionName) {
            $examSessionsFlags[$examSessionName] = false;
        }

        foreach($student->getExamStudents() as $examStudent) {
            $examSessionsFlags[$examStudent->getExamSession()->getExamClassification()->getExamSessionType()->getName()] = true;
        }

        return !in_array(false, $examSessionsFlags, true);
    }
}
