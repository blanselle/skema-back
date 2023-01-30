<?php

namespace App\Constants\Payment;

class PaymentModeConstants
{
    public const PAYMENT_MODE_BANK_TRANSFERT = 'bank_transfert';
    public const PAYMENT_MODE_CHECK = 'check';
    public const PAYMENT_MODE_ONLINE = 'online';
    public const PAYMENT_MODE_CASH = 'cash';
    public const PAYMENT_MODE_OTHER = 'other';
    public const PAYMENT_MANUAL_LIST = [
        'payment.mode.bank_transfert' => PaymentModeConstants::PAYMENT_MODE_BANK_TRANSFERT,
        'payment.mode.check' => PaymentModeConstants::PAYMENT_MODE_CHECK,
        'payment.mode.cash' => PaymentModeConstants::PAYMENT_MODE_CASH,
        'payment.mode.other' => PaymentModeConstants::PAYMENT_MODE_OTHER,
    ];
}