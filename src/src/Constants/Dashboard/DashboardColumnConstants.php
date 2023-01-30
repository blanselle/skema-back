<?php

declare(strict_types=1);

namespace App\Constants\Dashboard;

use App\Constants\ConstantsInterface;
use App\Constants\User\StudentWorkflowStateConstants;
use ReflectionClass;

class DashboardColumnConstants implements ConstantsInterface
{
    public const NB_TOTAL = [
        StudentWorkflowStateConstants::STATE_START,
        StudentWorkflowStateConstants::STATE_EXEMPTION,
        StudentWorkflowStateConstants::STATE_CHECK_DIPLOMA,
        StudentWorkflowStateConstants::STATE_CREATED,
        StudentWorkflowStateConstants::STATE_REJECTED,
        StudentWorkflowStateConstants::STATE_REJECTED_DIPLOMA,
        StudentWorkflowStateConstants::STATE_REJECTED_ELIGIBLE,
        StudentWorkflowStateConstants::STATE_DECLINED_PAYMENT,
        StudentWorkflowStateConstants::STATE_RESIGNATION_PAYMENT,
        StudentWorkflowStateConstants::STATE_RESIGNATION,
        StudentWorkflowStateConstants::STATE_CHECK_BOURSIER,
        StudentWorkflowStateConstants::STATE_CREATED_TO_PAY,
        StudentWorkflowStateConstants::STATE_VALID,
        StudentWorkflowStateConstants::STATE_ELIGIBLE,
        StudentWorkflowStateConstants::STATE_RECHECK_BOURSIER,
        StudentWorkflowStateConstants::STATE_COMPLETE,
        StudentWorkflowStateConstants::STATE_APPROVED,
        StudentWorkflowStateConstants::STATE_ADMISSIBLE,
        StudentWorkflowStateConstants::STATE_REJECTED_ADMISSIBLE,
        StudentWorkflowStateConstants::STATE_CANCELED,
        StudentWorkflowStateConstants::STATE_CANCELED_PAYMENT,
        StudentWorkflowStateConstants::STATE_BOURSIER_KO,
    ];

    public const START = [
        StudentWorkflowStateConstants::STATE_START,
    ];
    
    public const INITIALIZED_DEROGED = [
        StudentWorkflowStateConstants::STATE_EXEMPTION,
    ];
    public const INITIALIZED_CONTROL_ELIGIBLE = [
        StudentWorkflowStateConstants::STATE_CHECK_DIPLOMA,
    ];
    public const INITIALIZED = [
        StudentWorkflowStateConstants::STATE_CREATED,
        StudentWorkflowStateConstants::STATE_CREATED_TO_PAY,
        StudentWorkflowStateConstants::STATE_CHECK_BOURSIER,
        StudentWorkflowStateConstants::STATE_RECHECK_BOURSIER,
        StudentWorkflowStateConstants::STATE_BOURSIER_KO,
    ];
    public const IN_PROGRESS = [
        StudentWorkflowStateConstants::STATE_ELIGIBLE,
        StudentWorkflowStateConstants::STATE_VALID,
    ];
    public const VALIDATED = [
        StudentWorkflowStateConstants::STATE_COMPLETE,
    ];
    public const APPROVED = [
        StudentWorkflowStateConstants::STATE_APPROVED,
    ];
    public const REFUSED = [
        StudentWorkflowStateConstants::STATE_REJECTED,
        StudentWorkflowStateConstants::STATE_REJECTED_DIPLOMA,
        StudentWorkflowStateConstants::STATE_REJECTED_ELIGIBLE,
    ];
    public const ABORTED = [
        StudentWorkflowStateConstants::STATE_CANCELED,
        StudentWorkflowStateConstants::STATE_CANCELED_PAYMENT,
    ];

    public const RESIGNATION = [
        StudentWorkflowStateConstants::STATE_RESIGNATION,
        StudentWorkflowStateConstants::STATE_RESIGNATION_PAYMENT,
    ];

    public const DECLINED = [
        StudentWorkflowStateConstants::STATE_DECLINED_PAYMENT,
    ];

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
