<?php

declare(strict_types=1);

namespace App\Service\Workflow\Student;

use App\Constants\Media\MediaWorflowStateConstants;
use App\Constants\User\StudentWorkflowStateConstants;
use App\Constants\User\StudentWorkflowTransitionConstants;
use App\Entity\Student;
use App\Repository\MediaRepository;
use App\Service\Workflow\Media\MediaWorkflowManager;
use Symfony\Component\Workflow\Registry;

class StudentWorkflowManager
{
    public function __construct(
        private Registry $workflowRegistry,
        private MediaRepository $mediaRepository,
        private MediaWorkflowManager $mediaWorkflowManager
    ) {
    }

    public function admissibleToRegisteredEo(Student $student): void
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');
        if ($workflow->can($student, StudentWorkflowTransitionConstants::ADMISSIBLE_TO_REGISTERED_EO)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::ADMISSIBLE_TO_REGISTERED_EO);
        }
    }

    public function toRegisteredEo(Student $student): void
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');
        if ($workflow->can($student, StudentWorkflowTransitionConstants::ADMISSIBLE_TO_REGISTERED_EO)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::ADMISSIBLE_TO_REGISTERED_EO);
        }
        if ($workflow->can($student, StudentWorkflowTransitionConstants::CANCELED_EO_TO_REGISTERED_EO)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CANCELED_EO_TO_REGISTERED_EO);
        }
    }

    public function validateExemption(Student $student): void
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');
        if ($workflow->can($student, StudentWorkflowTransitionConstants::DEROGATE_TO_CREATED)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::DEROGATE_TO_CREATED);
        }

        if ($workflow->can($student, StudentWorkflowTransitionConstants::DEROGATE_TO_CHECK_DIPLOMA)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::DEROGATE_TO_CHECK_DIPLOMA);
        }
    }

    public function rejectedExemption(Student $student): void
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');
        
        if ($workflow->can($student, StudentWorkflowTransitionConstants::REJECT)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::REJECT);
        }
    }

    public function rejectCheckDiploma(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_REJECTED_DIPLOMA)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_REJECTED_DIPLOMA);

            return true;
        }

        return false;
    }

    public function acceptCheckDiploma(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_CREATED)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_CREATED);

            return true;
        }

        return false;
    }

    public function resignation(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::RESIGNATION_PAYED)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::RESIGNATION_PAYED);

            return true;
        }

        if ($workflow->can($student, StudentWorkflowTransitionConstants::RESIGNATION)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::RESIGNATION);

            return true;
        }

        return false;
    }

    public function cancelation(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::CANCELATION_PAYED) && false == $student->getAdministrativeRecord()?->getScholarShip()) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CANCELATION_PAYED);

            return true;
        }

        if ($workflow->can($student, StudentWorkflowTransitionConstants::CANCELATION)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CANCELATION);

            return true;
        }

        return false;
    }

    public function arToCheck(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::AR_TO_CHECK)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::AR_TO_CHECK);

            return true;
        }

        return false;
    }

    public function arValidated(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::AR_VALIDATED)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::AR_VALIDATED);

            return true;
        }

        return false;
    }

    public function valid(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::AR_VALIDATED_TO_VALID)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::AR_VALIDATED_TO_VALID);

            return true;
        }

        if ($workflow->can($student, StudentWorkflowTransitionConstants::CHECK_BOURSIER_TO_VALID)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CHECK_BOURSIER_TO_VALID);

            return true;
        }

        return false;
    }

    public function refuseScholarship(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::TO_BOURSIER_KO)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::TO_BOURSIER_KO);

            return true;
        }

        return false;
    }

    public function eligible(Student $student): void
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::VALID_TO_ELIGIBLE)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::VALID_TO_ELIGIBLE);

            return;
        }

        if ($workflow->can($student, StudentWorkflowTransitionConstants::CHECK_BOURSIER_TO_ELIGIBLE)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::CHECK_BOURSIER_TO_ELIGIBLE);

            return;
        }

        return;
    }

    public function approved(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::ELIGIBLE_TO_APPROVED)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::ELIGIBLE_TO_APPROVED);

            return true;
        }

        return false;
    }

    public function admissible(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::APPROVED_TO_ADMISSIBLE)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::APPROVED_TO_ADMISSIBLE);

            return true;
        }

        return false;
    }


    public function rejectedAdmissible(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::APPROVED_TO_REJECTED_ADMISSIBLE)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::APPROVED_TO_REJECTED_ADMISSIBLE);

            return true;
        }

        return false;
    }

    public function complete(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::TO_COMPLETE)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::TO_COMPLETE);

            // spec 15.660
            $medias = $this->mediaRepository->findBy(['student' => $student, 'state' => MediaWorflowStateConstants::STATE_UPLOADED]);
            foreach ($medias as $media) {
                $this->mediaWorkflowManager->uploadedToCheck($media);
            }

            return true;
        }

        return false;
    }

    public function eligibleToComplete(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');
        if ($workflow->can($student, StudentWorkflowTransitionConstants::ELIGIBLE_TO_COMPLETE)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::ELIGIBLE_TO_COMPLETE);
            
            return true;
        }

        return false;
    }

    public function declinedPayment(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::TO_DECLINED_PAYMENT)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::TO_DECLINED_PAYMENT);

            return true;
        }

        return false;
    }

    public function completeProof(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::TO_COMPLETE_PROOF)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::TO_COMPLETE_PROOF);

            return true;
        }

        return false;
    }

    public function isProfilStudentDisabled(Student $student): bool
    {
        if (in_array($student->getState(), [
            StudentWorkflowStateConstants::STATE_REJECTED,
            StudentWorkflowStateConstants::STATE_REJECTED_DIPLOMA,
            StudentWorkflowStateConstants::STATE_DECLINED_PAYMENT,
            StudentWorkflowStateConstants::STATE_REJECTED_ELIGIBLE,
            StudentWorkflowStateConstants::STATE_RESIGNATION,
            StudentWorkflowStateConstants::STATE_RESIGNATION_PAYMENT,
            StudentWorkflowStateConstants::STATE_CANCELED,
            StudentWorkflowStateConstants::STATE_CANCELED_PAYMENT,
        ], true)) {
            return true;
        }
        return false;
    }

    public function isBeingRegistered(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if (
            $workflow->can($student, StudentWorkflowTransitionConstants::SUBMIT_TO_EXEMPTION) or
            $workflow->can($student, StudentWorkflowTransitionConstants::SUBMIT_TO_CHECK_DIPLOMA) or
            $workflow->can($student, StudentWorkflowTransitionConstants::SUBMIT_TO_CREATED) or
            $workflow->can($student, StudentWorkflowTransitionConstants::DEROGATE_TO_CHECK_DIPLOMA) or
            $workflow->can($student, StudentWorkflowTransitionConstants::DEROGATE_TO_CREATED) or
            $workflow->can($student, StudentWorkflowTransitionConstants::CHECK_DIPLOMA_TO_CREATED) or
            StudentWorkflowStateConstants::STATE_CREATED === $student->getState()
        ) {
            return true;
        }

        return false;
    }
    
    public function activeAccount(Student $student): void
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::SUBMIT_TO_EXEMPTION)) {
            $student->setTransition(StudentWorkflowTransitionConstants::SUBMIT_TO_EXEMPTION);
            $workflow->apply($student, StudentWorkflowTransitionConstants::SUBMIT_TO_EXEMPTION);
        }
        if ($workflow->can($student, StudentWorkflowTransitionConstants::SUBMIT_TO_CHECK_DIPLOMA)) {
            $student->setTransition(StudentWorkflowTransitionConstants::SUBMIT_TO_CHECK_DIPLOMA);
            $workflow->apply($student, StudentWorkflowTransitionConstants::SUBMIT_TO_CHECK_DIPLOMA);
        }
        if ($workflow->can($student, StudentWorkflowTransitionConstants::SUBMIT_TO_CREATED)) {
            $student->setTransition(StudentWorkflowTransitionConstants::SUBMIT_TO_CREATED);
            $workflow->apply($student, StudentWorkflowTransitionConstants::SUBMIT_TO_CREATED);
        }
    }

    public function registeredSk(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::ADMIS_TO_REGISTERED_SK)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::ADMIS_TO_REGISTERED_SK);

            return true;
        }

        return false;
    }

    public function completeToApproved(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::COMPLETE_TO_APPROVED)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::COMPLETE_TO_APPROVED);

            return true;
        }

        return false;
    }

    public function recheckBoursier(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::BOURSIER_KO_TO_RECHECK_BOURSIER)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::BOURSIER_KO_TO_RECHECK_BOURSIER);

            return true;
        }

        return false;
    }

    public function createdToPay(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::RECHECK_BOURSIER_TO_CREATED_TO_PAY)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::RECHECK_BOURSIER_TO_CREATED_TO_PAY);

            return true;
        }

        return false;
    }

    public function registeredEoToCancelEo(Student $student): bool
    {
        $workflow = $this->workflowRegistry->get($student, 'candidate');

        if ($workflow->can($student, StudentWorkflowTransitionConstants::REGISTERED_EO_TO_CANCELED_EO)) {
            $workflow->apply($student, StudentWorkflowTransitionConstants::REGISTERED_EO_TO_CANCELED_EO);

            return true;
        }

        return false;
    }
}
