<?php

declare(strict_types=1);

namespace App\EventSubscriber\Workflow\Student;

use App\Constants\Media\MediaCodeConstants;
use App\Constants\User\StudentWorkflowTransitionConstants;
use App\Entity\Student;
use App\Helper\CacheHelper;
use App\Manager\CandidacyManager;
use App\Manager\StudentManager;
use App\Service\CandidateManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GuardSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CandidateManager $candidateManager,
        private StudentManager $studentManager,
        private CandidacyManager $candidacyManager,
        private CacheHelper $cacheHelper
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::SUBMIT_TO_EXEMPTION
            ) => 'submitToExemption',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::SUBMIT_TO_CREATED
            ) => 'submitToCreated',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::SUBMIT_TO_CHECK_DIPLOMA
            ) => 'submitToCheckDiploma',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::RESIGNATION_PAYED
            ) => 'resignationPayed',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::AR_TO_CHECK
            ) => 'arToCheck',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::AR_VALIDATED
            ) => 'arValidated',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::DEROGATE_TO_CREATED
            ) => 'derogateToCreated',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::VALID_TO_ELIGIBLE
            ) => 'validToEligible',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::CHECK_BOURSIER_TO_VALID
            ) => 'boursierToValid',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::CHECK_BOURSIER_TO_ELIGIBLE
            ) => 'boursierToEligible',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::ELIGIBLE_TO_APPROVED
            ) => 'eligibleToApproved',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::APPROVED_TO_ADMISSIBLE
            ) => 'approvedToAdmissible',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::APPROVED_TO_REJECTED_ADMISSIBLE
            ) => 'approvedToRejectedAdmissible',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::ADMIS_TO_REGISTERED_SK
            ) => 'admisToRegisteredSk',            
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::AR_VALIDATED_TO_VALID
            ) => 'arValidatedToValid',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::TO_COMPLETE
            ) => 'toComplete',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::TO_BOURSIER_KO
            ) => 'toBoursierKO',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::ELIGIBLE_TO_COMPLETE
            ) => 'eligibleToComplete',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::COMPLETE_TO_APPROVED
            ) => 'completeToApproved',
            sprintf(
                StudentWorkflowTransitionConstants::BOURSIER_KO_TO_RECHECK_BOURSIER
            ) => 'boursierKoToRecheckBoursier',
            sprintf(
                'workflow.candidate.guard.%s',
                StudentWorkflowTransitionConstants::RECHECK_BOURSIER_TO_CREATED_TO_PAY
            ) => 'recheckBoursierToCreatedToPay',
        ];
    }
    
    public function derogateToCreated(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if ($this->candidateManager->hasOtherDiploma($student)) {
            $event->setBlocked(true);
        }
    }

    public function submitToExemption(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if ($this->candidateManager->isOlderThanLimit($student)) {
            return;
        }

        $event->setBlocked(true);
    }

    public function submitToCreated(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if ($this->candidateManager->hasOtherDiploma($student)) {
            $event->setBlocked(true);
        }

        if (!$this->candidateManager->isOlderThanLimit($student)) {
            return;
        }

        $event->setBlocked(true);
    }

    public function submitToCheckDiploma(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if ($this->candidateManager->isOlderThanLimit($student)) {
            $event->setBlocked(true);
        }

        if (!$this->candidateManager->hasOtherDiploma($student)) {
            $event->setBlocked(true);
        }
    }

    public function resignationPayed(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (!$this->candidateManager->isDateInscriptionTooLate(programChannel: $student->getProgramChannel()) && true === $student->getCompetitionFeesPayment()) {
            return;
        }

        $event->setBlocked(true);
    }

    public function arToCheck(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (true !== $student->getAdministrativeRecord()?->getScholarShip()) {
            // Non boursier
            $event->setBlocked(true);
            return;
        }
    }

    public function arValidated(GuardEvent $event): void
    {
        $student = $event->getSubject();
        
        if (!$student instanceof Student) {
            return;
        }

        if (false !== $student->getAdministrativeRecord()?->getScholarShip()) {
            // Boursier
            $event->setBlocked(true);
        }
    }

    public function validToEligible(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (!$this->candidateManager->hasAcceptedDocumentByCode($student, MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE)) {
            $event->setBlocked(true);
            return;
        }
    }

    public function boursierToEligible(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (!$this->candidateManager->hasAcceptedDocumentByCode($student, MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE)) {
            $event->setBlocked(true);
            return;
        }

        if (!$this->candidateManager->hasAllAcceptedDocumentsByCode($student, MediaCodeConstants::CODE_CROUS)) {
            $event->setBlocked(true);
            return;
        }
    }

    public function boursierToValid(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (!$this->candidateManager->hasAllAcceptedDocumentsByCode($student, MediaCodeConstants::CODE_CROUS)) {
            $event->setBlocked(true);
            return;
        }

        if ($this->candidateManager->hasAcceptedDocumentByCode($student, MediaCodeConstants::CODE_CERTIFICAT_ELIGIBILITE)) {
            $event->setBlocked(true);
            return;
        }
    }

    public function arValidatedToValid(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if(false === $this->studentManager->studentHasPayedOrIsScholarShip($student)) {
            $event->setBlocked(true);
            return;
        }
    }

    public function eligibleToApproved(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }
    }

    public function approvedToAdmissible(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        $this->cacheHelper->getAdmissibilityResult(student: $student, key: CacheHelper::ADMISSIBILITY_RESULT_OK);
    }

    public function approvedToRejectedAdmissible(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        $this->cacheHelper->getAdmissibilityResult(student: $student, key: CacheHelper::ADMISSIBILITY_RESULT_KO);
    }

    public function admisToRegisteredSk(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }
    }

    public function toComplete(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if(false === $this->candidacyManager->allValidated($student)) {
            $event->setBlocked(true);
        }
    }

    public function toBoursierKO(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (0 === $student->getAdministrativeRecord()->getScholarShipMedias()->count()) {
            $event->setBlocked(true);
        }
    }

    /**
     * https://pictime.atlassian.net/browse/SB-1154
     */
    public function eligibleToComplete(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if(!$this->candidacyManager->administrativeRecordValidatedForStudent($student)) {
            $event->setBlocked(true);
            return;
        }

        // https://pictime.atlassian.net/browse/SB-1328
        if(!$this->candidacyManager->writtenExaminationValidated($student)) {
            $event->setBlocked(true);
            return;
        }

        if(false === $student->getCv()?->getValidated()) {
            $event->setBlocked(true);
        }
    }

    public function completeToApproved(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }

        if (!$this->candidacyManager->hasAllAcceptedDocumentsToCompleteCandidacy(student: $student)) {
            $event->setBlocked(true);
        }
    }

    public function boursierKoToRecheckBoursier(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }
    }

    public function recheckBoursierToCreatedToPay(GuardEvent $event): void
    {
        $student = $event->getSubject();

        if (!$student instanceof Student) {
            return;
        }
    }
}
