<?php

namespace App\Service\Payment;

use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\PaymentConstants;
use App\Constants\Payment\PaymentExternalStatusConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Exception\Parameter\ParameterNotFoundException;
use App\Exception\Payment\PaymentInvalidArgumentException;
use App\Exception\Payment\PaymentResponseException;
use App\Helper\PaymentHelper;
use App\Repository\Parameter\ParameterRepository;
use App\Repository\Payment\OrderRepository;
use App\Repository\Payment\PaymentRepository;
use App\Service\Workflow\Payment\OrderWorkflowManager;
use App\Service\Workflow\Payment\PaymentWorkflowManager;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class PaymentManager
{
    public function __construct(
        private OrderRepository $orderRepository,
        private OrderWorkflowManager $orderWorkflowManager,
        private ParameterRepository $parameterRepository,
        private PaymentHelper $paymentHelper,
        private PaymentRepository $paymentRepository,
        private PaymentWorkflowManager $paymentWorkflowManager,
        private TranslatorInterface $translator,
        private LoggerInterface $paymentLogger
    ) {}

    public function requestPayment(Payment $payment): string
    {
        $order = $payment->getIndent();

        $amount = $this->getPaymentAmount($order);

        if (null === $amount) {
            throw new BadRequestHttpException('Amount cannot be calculated');
        }

        $order->setAmount($amount);
        $this->orderRepository->save($order, true);

        $this->paymentRepository->save($payment, true);

        // Add payment id in query parameter because the form redirect to an another page.
        // With this query parameter we can found our payment.
        $queryString = http_build_query(['paymentId'=> $payment->getId()]);
        $redirectUrl = append_query_string($payment->getRedirectUrl(), $queryString, APPEND_QUERY_STRING_REPLACE_DUPLICATE);

        $payment->setRedirectUrl($redirectUrl);
        $this->paymentRepository->save($payment, true);

        $references = $this->getReferences(payment: $payment);

        $createHostedCheckoutResponse = $this->paymentHelper->createHostedCheckout(
            amount: $amount,
            redirectUrl: $redirectUrl,
            references: $references);

        $payment->setExternalReturnMAC($createHostedCheckoutResponse->getRETURNMAC());
        $payment->setExternalHostedCheckoutId($createHostedCheckoutResponse->getHostedCheckoutId());
        $payment->setExternalPaymentID("{$createHostedCheckoutResponse->getHostedCheckoutId()}_0");
        $this->paymentRepository->save($payment, true);

        $this->orderWorkflowManager->treated(order: $payment->getIndent());

        return $createHostedCheckoutResponse->getRedirectUrl();
    }

    /**
     * @codeCoverageIgnore
     */
    public function sendCapturePayment(Payment $payment): Payment
    {
        // Need to refresh payment status before the capture in case of payment rejected
        $paymentResponse = $this->paymentHelper->getPayment(paymentID: $payment->getExternalPaymentID());
        $payment->setExternalStatus($paymentResponse->getStatus());
        $this->paymentRepository->save($payment, true);

        $this->paymentWorkflowManager->evolute(payment: $payment);

        if ($payment->getExternalStatus() === PaymentExternalStatusConstants::EXTERNAL_STATE_PENDING_CAPTURE) {
            $this->paymentLogger->info("Request capture payment for {$payment->getExternalPaymentID()} and amount {$payment->getIndent()->getAmount()}");

            $captureResponse = $this->paymentHelper->requestCapturePayment(paymentID: $payment->getExternalPaymentID(), amount: $payment->getIndent()->getAmount());

            $payment->setExternalStatus($captureResponse->getStatus());
            $this->paymentRepository->save($payment, true);

            $this->paymentWorkflowManager->evolute(payment: $payment);
        }

        return $payment;
    }

    /**
     * @codeCoverageIgnore
     */
    public function requestPaymentStatus(Payment $payment): Payment
    {
        if (empty($payment->getExternalPaymentID())) {
            throw new PaymentInvalidArgumentException(paymentId: $payment->getId());
        }

        try {
            $paymentResponse = $this->paymentHelper->getPayment(paymentID: $payment->getExternalPaymentID());
            $payment->setExternalStatus($paymentResponse->getStatus());
            $this->paymentRepository->save($payment, true);
            $this->paymentWorkflowManager->evolute(payment: $payment);
        } catch (Exception $e) {
            // In case of Invalid or Incomplete transaction (see: https://support.direct.ingenico.com/en/documentation/api/statuses)
            if (PaymentWorkflowStateConstants::STATE_CREATED === $payment->getState() and null === $payment->getExternalStatus()) {
                $this->paymentWorkflowManager->forceCancelPayment(payment: $payment);
            }

            throw new PaymentResponseException(paymentId: $payment->getExternalPaymentID());
        }

        // If payment is captured we need to call captures to get the real payment's status
        try {
            $capturesResponse = $this->paymentHelper->getCapturesOfPayment(paymentID: $payment->getExternalPaymentID());
            foreach ($capturesResponse->getCaptures() as $capture) {
                $payment->setExternalStatus($capture->getStatus());
                $this->paymentRepository->save($payment, true);

                $this->paymentWorkflowManager->evolute(payment: $payment);
            }
        } catch (Exception $e) {
            // No capture found
            $this->paymentLogger->error($e->getMessage());
        }

        return $payment;
    }

    public function updatePaymentsFromWebhooksEvent(mixed $transaction): void
    {
        $this->updatePaymentFromWebhooksEvent(transaction: $transaction);

        if (PaymentExternalStatusConstants::EXTERNAL_STATE_PENDING_CAPTURE === $transaction->payment->status) {
            $this->paymentLogger->info("Request capture payment for {$transaction->payment->id} and amount {$transaction->payment->paymentOutput->amountOfMoney->amount}");

            $captureResponse = $this->paymentHelper->requestCapturePayment(
                paymentID: $transaction->payment->id,
                amount: $transaction->payment->paymentOutput->amountOfMoney->amount
            );
        }
    }

    private function updatePaymentFromWebhooksEvent(mixed $transaction): void
    {
        $merchantReference = $transaction->payment->paymentOutput->references->merchantReference;
        $payment = $this->findPayment(criteria: ['merchantReference' => $merchantReference]);
        $payment->setExternalStatus($transaction->payment->status);
        $this->paymentRepository->save($payment, true);

        $this->paymentWorkflowManager->evolute(payment: $payment);
    }

    public function findPayment(array $criteria): Payment
    {
        $payment = $this->paymentRepository->findOneBy($criteria);
        if (null === $payment) {
            $str = json_encode($criteria);
            throw new BadRequestHttpException("Payment not found with {$str}");
        }

        return $payment;
    }

    private function getAmount(string $price): int
    {
        return (int) ((float)$price * 100);
    }

    private function getReferences(Payment $payment): array
    {
        $order = $payment->getIndent();
        $type = $order->getType();
        $references = [];

        $merchantParametersData = [
            'type' => $type,
            'studentIdentifier' => $order->getStudent()->getIdentifier(),
        ];

        if ($type === OrderTypeConstants::SCHOOL_REGISTRATION_FEES) {
            $references = [
                'descriptor' => $this->translator->trans("order.type.{$type}"),
                'merchantParameters' => http_build_query($merchantParametersData),
                'merchantReference' => $payment->getMerchantReference(),
            ];
        }

        if ($type === OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION) {
            $merchantParametersData['examSessionId'] = $order->getExamSession()->getId();
            $references = [
                'descriptor' => $this->translator->trans("order.type.{$type}", [
                    '%session_name%' => $order->getExamSession()->getExamClassification()->getName()
                ]),
                'merchantParameters' => http_build_query($merchantParametersData),
                'merchantReference' => $payment->getMerchantReference(),
            ];
        }

        if (null === ($references['merchantReference']?? null)) {
            throw new Exception('The merchant reference is required');
        }

        return $references;
    }

    public function getPaymentAmount(Order $order): int|null
    {
        $amount = null;
        $type = $order->getType();

        if ($type === OrderTypeConstants::SCHOOL_REGISTRATION_FEES) {
            $parameter = $this->parameterRepository->findOneParameterByKeyNameAndProgramChannel(
                key: PaymentConstants::PARAMETER_KEY_REGISTRATION_FEES,
                programChannel: $order->getStudent()->getProgramChannel()
            );
            if (null === $parameter) {
                throw new ParameterNotFoundException(PaymentConstants::PARAMETER_KEY_REGISTRATION_FEES);
            }

            $amount = $this->getAmount($parameter->getValue());
        }

        if ($type === OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION) {
            $amount = $this->getAmount($order->getExamSession()->getPrice());
        }

        return $amount;
    }
}