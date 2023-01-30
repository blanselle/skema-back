<?php

namespace App\Constants\Payment;

class PaymentWorkflowStateConstants
{
    public const STATE_CREATED = 'created';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_VALIDATED = 'validated';
    public const STATE_REJECTED = 'rejected';
    public const STATE_CANCELED = 'canceled';
}