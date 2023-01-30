<?php

namespace App\Exception\Payment;

use Exception;

class PaymentInvalidArgumentException extends Exception
{
    public function __construct(int $paymentId)
    {
        parent::__construct(
            message: sprintf('Impossible de mettre à jour le statut du paiement %d : PSP Reference est vide. Merci de contacter votre administrateur si vous souhaitez annuler le paiement.', $paymentId),
            code: 500,
        );
    }
}