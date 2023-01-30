<?php

namespace App\Constants\Payment;

use App\Constants\ConstantsInterface;
use ReflectionClass;

class PaymentExternalStatusConstants implements ConstantsInterface
{
    /** @see https://support.direct.ingenico.com/en/documentation/api/reference/#tag/Payments/operation/GetPaymentApi */
    public const EXTERNAL_STATE_CREATED = 'CREATED';
    public const EXTERNAL_STATE_CANCELLED = 'CANCELLED';
    public const EXTERNAL_STATE_REJECTED = 'REJECTED';
    public const EXTERNAL_STATE_REJECTED_CAPTURE = 'REJECTED_CAPTURE';
    public const EXTERNAL_STATE_REDIRECTED = 'REDIRECTED';
    public const EXTERNAL_STATE_PENDING_PAYMENT = 'PENDING_PAYMENT';
    public const EXTERNAL_STATE_PENDING_COMPLETION = 'PENDING_COMPLETION';
    public const EXTERNAL_STATE_PENDING_CAPTURE = 'PENDING_CAPTURE';
    public const EXTERNAL_STATE_AUTHORIZATION_REQUESTED = 'AUTHORIZATION_REQUESTED';
    public const EXTERNAL_STATE_CAPTURE_REQUESTED = 'CAPTURE_REQUESTED';
    public const EXTERNAL_STATE_CAPTURED = 'CAPTURED';
    public const EXTERNAL_STATE_REVERSED = 'REVERSED';
    public const EXTERNAL_STATE_REFUND_REQUESTED = 'REFUND_REQUESTED';
    public const EXTERNAL_STATE_REFUNDED = 'REFUNDED';

    public static function getConsts(): array
    {
        $class = new ReflectionClass(self::class);

        return $class->getConstants();
    }
}