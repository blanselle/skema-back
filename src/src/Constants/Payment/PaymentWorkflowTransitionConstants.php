<?php

namespace App\Constants\Payment;

class PaymentWorkflowTransitionConstants
{
    // transition constants
    public const TRANSITION_TREATED = 'treated';
    public const TRANSITION_VALIDATE = 'validate';
    public const TRANSITION_REJECT = 'reject';
    public const TRANSITION_CANCEL = 'cancel';

    // external state to make transition
    public const TO_TREATED = array(
        PaymentExternalStatusConstants::EXTERNAL_STATE_REDIRECTED,
        PaymentExternalStatusConstants::EXTERNAL_STATE_PENDING_PAYMENT,
        PaymentExternalStatusConstants::EXTERNAL_STATE_PENDING_COMPLETION,
        PaymentExternalStatusConstants::EXTERNAL_STATE_PENDING_CAPTURE,
        PaymentExternalStatusConstants::EXTERNAL_STATE_AUTHORIZATION_REQUESTED,
        PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURE_REQUESTED,
    );
    public const TO_VALIDATE = [
        PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURED,
    ];
    public const TO_REJECT = [
        PaymentExternalStatusConstants::EXTERNAL_STATE_REJECTED,
        PaymentExternalStatusConstants::EXTERNAL_STATE_REJECTED_CAPTURE,
        PaymentExternalStatusConstants::EXTERNAL_STATE_REVERSED,
        PaymentExternalStatusConstants::EXTERNAL_STATE_REFUND_REQUESTED,
        PaymentExternalStatusConstants::EXTERNAL_STATE_REFUNDED,
    ];
    public const TO_CANCEL = [
        PaymentExternalStatusConstants::EXTERNAL_STATE_CANCELLED,
    ];
}