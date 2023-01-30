<?php

namespace App\Handler;

use App\Exception\Payment\PaymentInvalidArgumentException;
use App\Exception\Payment\PaymentResponseException;
use App\Message\PaymentsStatusMessage;
use App\Repository\Payment\PaymentRepository;
use App\Service\Payment\PaymentManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PaymentsStatusHandler implements MessageHandlerInterface
{
    public function __construct(
        private PaymentRepository $paymentRepository,
        private PaymentManager $paymentManager,
        private LoggerInterface $paymentLogger
    ) {}

    public function __invoke(PaymentsStatusMessage $message): void
    {
        $ids = $message->getPaymentsId();
        foreach ($ids as $paymentId) {
            $payment = $this->paymentRepository->find($paymentId);
            if (null === $payment) {
                continue;
            }
            
            $this->paymentLogger->info(message: "Request payment status for payment {$payment->getExternalPaymentID()}");

            try {
                $this->paymentManager->requestPaymentStatus(payment: $payment);
                $this->paymentLogger->info(message: "Status updated for payment {$payment->getExternalPaymentID()}");
            } catch (PaymentResponseException|PaymentInvalidArgumentException $e) {
                $this->paymentLogger->error(message: "Le paiement {$paymentId} ne peut être mis à jour par Ogone.");
            } catch (Exception $e) {
                $this->paymentLogger->error(message: $e->getMessage());
            }
        }
    }
}