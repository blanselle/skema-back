<?php

declare(strict_types=1);

namespace App\Constants\User;

use App\Constants\ConstantsInterface;
use ReflectionClass;

final class StudentWorkflowStateConstants implements ConstantsInterface
{
    public const STATE_START = 'start';
    public const STATE_EXEMPTION = 'exemption';
    public const STATE_CHECK_DIPLOMA = 'check_diploma';
    public const STATE_CREATED = 'created';
    public const STATE_REJECTED = 'rejected';
    public const STATE_REJECTED_DIPLOMA = 'rejected_diploma';
    public const STATE_REJECTED_ELIGIBLE = 'rejected_eligible';
    public const STATE_DECLINED_PAYMENT = 'declined_payment';
    public const STATE_RESIGNATION = 'resigned';
    public const STATE_RESIGNATION_PAYMENT = 'resigned_payed';
    public const STATE_CANCELED = 'canceled';
    public const STATE_CANCELED_PAYMENT = 'canceled_payed';
    public const STATE_CHECK_BOURSIER = 'check_boursier';
    public const STATE_RECHECK_BOURSIER = 'recheck_boursier';
    public const STATE_CREATED_TO_PAY = 'created_to_pay';
    public const STATE_VALID = 'valid';
    public const STATE_ELIGIBLE = 'eligible';
    public const STATE_APPROVED = 'approved';
    public const STATE_ADMISSIBLE = 'admissible';
    public const STATE_REJECTED_ADMISSIBLE = 'rejected_admissible';
    public const STATE_BOURSIER_KO = 'boursier_ko';
    public const STATE_COMPLETE = 'complete';
    public const STATE_COMPLETE_PROOF = 'complete_proof';
    public const ADMIS = 'admis';
    public const REGISTERED_SK = 'registered_sk';
    public const REGISTERED_EO = 'registered_eo';
    public const STATE_CANCELED_EO = 'canceled_eo';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}
