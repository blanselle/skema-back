<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class SimplifiedStudentStatusConstants implements ConstantsInterface
{
    public const INITIALIZED = [
        StudentWorkflowStateConstants::STATE_CHECK_DIPLOMA,
        StudentWorkflowStateConstants::STATE_CREATED,
        StudentWorkflowStateConstants::STATE_CREATED_TO_PAY,
    ];
    public const IN_PROGRESS = [
        StudentWorkflowStateConstants::STATE_CHECK_BOURSIER,
        StudentWorkflowStateConstants::STATE_RECHECK_BOURSIER,
        StudentWorkflowStateConstants::STATE_BOURSIER_KO,
        StudentWorkflowStateConstants::STATE_VALID,
        StudentWorkflowStateConstants::STATE_ELIGIBLE,
    ];
    public const COMPLETE = [
        StudentWorkflowStateConstants::STATE_COMPLETE,
        StudentWorkflowStateConstants::STATE_COMPLETE_PROOF,
    ];
    public const VALIDATED = [
        StudentWorkflowStateConstants::STATE_APPROVED,
    ];
    public const PLANIFICATION = [
        StudentWorkflowStateConstants::STATE_ADMISSIBLE,
    ];
    public const ORAL = [
         StudentWorkflowStateConstants::REGISTERED_EO,
    ];
    public const AFFECTATION_CHOICE = [
        // TODO: StudentWorkflowStateConstants::COMPLETE_EPREUVE_ECRITE,
        // TODO: StudentWorkflowStateConstants::FINALIZED_EPREUVE_ECRITE,
    ];
    public const ADMISSION = [
        // TODO: StudentWorkflowStateConstants::ADMIS_AFFECTED,
        StudentWorkflowStateConstants::ADMIS,
        StudentWorkflowStateConstants::REGISTERED_SK,
    ];
    
    public const UNAUTHORIZED = [
        StudentWorkflowStateConstants::STATE_START,
        StudentWorkflowStateConstants::STATE_EXEMPTION,
        StudentWorkflowStateConstants::STATE_REJECTED,
        StudentWorkflowStateConstants::STATE_REJECTED_DIPLOMA,
        StudentWorkflowStateConstants::STATE_REJECTED_ELIGIBLE,
        StudentWorkflowStateConstants::STATE_DECLINED_PAYMENT,
        StudentWorkflowStateConstants::STATE_RESIGNATION_PAYMENT,
        StudentWorkflowStateConstants::STATE_RESIGNATION,
        StudentWorkflowStateConstants::STATE_REJECTED_ADMISSIBLE,
        StudentWorkflowStateConstants::STATE_CANCELED,
        StudentWorkflowStateConstants::STATE_CANCELED_PAYMENT,
        StudentWorkflowStateConstants::STATE_CANCELED_EO,
    ];

    public static function getFromStatus(string $status): ?string
    {
        foreach(self::getConsts() as $simplifiedStatus => $consts) {
            foreach($consts as $const) {
                if($status === $const) {
                    return $simplifiedStatus;
                }
            }
        }
        
        return null;
    }

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
