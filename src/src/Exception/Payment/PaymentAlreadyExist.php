<?php

namespace App\Exception\Payment;

use Exception;

class PaymentAlreadyExist extends Exception
{
    public function __construct(string $type = '')
    {
        parent::__construct(
            message: sprintf('Le paiement pour "%s" est déjà enregistré. En cas de problème merci de nous contacter.', $type),
            code: 500,
        );
    }
}