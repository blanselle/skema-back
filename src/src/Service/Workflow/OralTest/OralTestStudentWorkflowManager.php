<?php

declare(strict_types=1);

namespace App\Service\Workflow\OralTest;

use App\Constants\OralTest\OralTestStudentWorkflowTransitionConstants;
use App\Entity\OralTest\OralTestStudent;
use Symfony\Component\Workflow\Registry;

class OralTestStudentWorkflowManager
{
    public function __construct(private Registry $workflowRegistry)
    {
    }

    public function validate(OralTestStudent $oralTestStudent): void
    {
        $workflow = $this->workflowRegistry->get($oralTestStudent, 'oral_test_student');
        if ($workflow->can($oralTestStudent, OralTestStudentWorkflowTransitionConstants::VALIDATE)) {
            $workflow->apply($oralTestStudent, OralTestStudentWorkflowTransitionConstants::VALIDATE);
        }
    }

    /**
     * This transition is equal to validate except the surbooking checking on guard
     */
    public function validateForce(OralTestStudent $oralTestStudent): void
    {
        $workflow = $this->workflowRegistry->get($oralTestStudent, 'oral_test_student');
        if ($workflow->can($oralTestStudent, OralTestStudentWorkflowTransitionConstants::VALIDATE_FORCE)) {
            $workflow->apply($oralTestStudent, OralTestStudentWorkflowTransitionConstants::VALIDATE_FORCE);
        }
    }

    public function reject(OralTestStudent $oralTestStudent): void
    {
        $workflow = $this->workflowRegistry->get($oralTestStudent, 'oral_test_student');
        if ($workflow->can($oralTestStudent, OralTestStudentWorkflowTransitionConstants::REJECT)) {
            $workflow->apply($oralTestStudent, OralTestStudentWorkflowTransitionConstants::REJECT);
        }
    }
}
