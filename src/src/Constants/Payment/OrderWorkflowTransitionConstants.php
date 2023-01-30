<?php

namespace App\Constants\Payment;

class OrderWorkflowTransitionConstants
{
    public const TRANSITION_TREATED = 'treated';
    public const TRANSITION_VALIDATE = 'validate';
    public const TRANSITION_RECREATE = 'recreate';
}