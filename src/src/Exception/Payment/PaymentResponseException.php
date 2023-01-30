<?php

namespace App\Exception\Payment;

use Exception;

class PaymentResponseException extends Exception
{
    public function __construct(string $paymentId = '')
    {
        parent::__construct(
            message: sprintf('Le paiement "%s" est peut être Invalid ou Incomplet. Merci de contacter votre administrateur si vous souhaitez annuler le paiement.', $paymentId),
            code: 500,
        );
    }
}