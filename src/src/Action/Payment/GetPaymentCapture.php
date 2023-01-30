<?php

namespace App\Action\Payment;

use App\Entity\Payment\Payment;
use App\Service\Payment\PaymentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * @codeCoverageIgnore
 */
#[AsController]
class GetPaymentCapture extends AbstractController
{
    public function __construct(private PaymentManager $paymentManager) {}

    public function __invoke(Payment $data): Payment
    {
        return $this->paymentManager->sendCapturePayment(payment: $data);
    }
}