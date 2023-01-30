<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class StudentWorkflowTransitionConstants implements ConstantsInterface
{
    public const SUBMIT_TO_EXEMPTION = 'submit_to_exemption';
    public const SUBMIT_TO_CHECK_DIPLOMA = 'submit_to_check_diploma';
    public const SUBMIT_TO_CREATED = 'submit_to_created';
    public const DEROGATE_TO_CHECK_DIPLOMA = 'derogate_to_check_diploma';
    public const DEROGATE_TO_CREATED = 'derogate_to_created';
    public const CHECK_DIPLOMA_TO_CREATED = 'check_diploma_to_created';
    public const REJECT = 'reject';
    public const RESIGNATION = 'resignation';
    public const RESIGNATION_PAYED = 'resignation_payed';
    public const CANCELATION = 'cancelation';
    public const CANCELATION_PAYED = 'cancelation_payed';
    public const AR_TO_CHECK = 'ar_to_check';
    public const AR_VALIDATED = 'ar_validated';
    public const CHECK_DIPLOMA_TO_REJECTED_DIPLOMA = 'check_diploma_to_rejected_diploma';
    public const AR_VALIDATED_TO_VALID = 'ar_validated_to_valid';
    public const VALID_TO_ELIGIBLE = 'valid_to_eligible';
    public const CHECK_BOURSIER_TO_VALID = 'check_boursier_to_valid';
    public const CHECK_BOURSIER_TO_ELIGIBLE = 'check_boursier_to_eligible';
    public const ELIGIBLE_TO_APPROVED = 'eligible_to_approved';
    public const APPROVED_TO_ADMISSIBLE = 'approved_to_admissible';
    public const APPROVED_TO_REJECTED_ADMISSIBLE = 'approved_to_rejected_admissible';
    public const TO_COMPLETE = 'to_complete';
    public const TO_COMPLETE_PROOF = 'to_complete_proof';
    public const TO_DECLINED_PAYMENT = 'to_declined_payment';
    public const ADMIS_TO_REGISTERED_SK = 'registered_sk';
    public const TO_BOURSIER_KO = 'to_boursier_ko';
    public const ELIGIBLE_TO_COMPLETE = 'eligible_to_complete';
    public const COMPLETE_TO_APPROVED = 'complete_to_approved';
    public const BOURSIER_KO_TO_RECHECK_BOURSIER = 'boursier_ko_to_recheck_boursier';
    public const RECHECK_BOURSIER_TO_CREATED_TO_PAY = 'recheck_boursier_to_created_to_pay';
    public const ADMISSIBLE_TO_REGISTERED_EO = 'admissibile_to_registered_eo';
    public const REGISTERED_EO_TO_CANCELED_EO = 'registered_eo_to_canceled_eo';
    public const CANCELED_EO_TO_REGISTERED_EO = 'canceled_eo_to_registered_eo';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
