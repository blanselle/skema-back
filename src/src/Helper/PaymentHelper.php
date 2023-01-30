<?php

namespace App\Helper;

use App\Constants\Payment\PaymentConstants;
use OnlinePayments\Sdk\Client;
use OnlinePayments\Sdk\Communicator;
use OnlinePayments\Sdk\CommunicatorConfiguration;
use OnlinePayments\Sdk\DefaultConnection;
use OnlinePayments\Sdk\Domain\AmountOfMoney;
use OnlinePayments\Sdk\Domain\CapturePaymentRequest;
use OnlinePayments\Sdk\Domain\CaptureResponse;
use OnlinePayments\Sdk\Domain\CapturesResponse;
use OnlinePayments\Sdk\Domain\CardPaymentMethodSpecificInput;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutRequest;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutResponse;
use OnlinePayments\Sdk\Domain\HostedCheckoutSpecificInput;
use OnlinePayments\Sdk\Domain\Order;
use OnlinePayments\Sdk\Domain\OrderReferences;
use OnlinePayments\Sdk\Domain\PaymentResponse;
use OnlinePayments\Sdk\Merchant\MerchantClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class PaymentHelper
{
    public function __construct(
        #[Autowire('%payment_merchant_id%')]
        private string $paymentMerchantId,
        #[Autowire('%payment_api_key%')]
        private string $paymentApiKey,
        #[Autowire('%payment_api_secret%')]
        private string $paymentApiSecret,
        #[Autowire('%payment_api_endpoint%')]
        private string $paymentApiEndpoint,
        #[Autowire('%payment_integrator%')]
        private string $paymentIntegrator
    ) {}

    /**
     * @see https://support.direct.ingenico.com/en/documentation/api/reference/#tag/HostedCheckout/operation/CreateHostedCheckoutApi
     * references = [
     *   'descriptor' => 'Descriptive text that is used towards to customer',
     *   'merchantReference' => 'Your unique reference of the transaction that is also returned in our report files (required)',
     *   'merchantParameters' => 'It allows you to store additional parameters for the transaction (Ex: type=TYPE&studentIdentifier=STUDENT_IDENTIFIER&examSessionId=EXAM_SESSION_ID)',
     * ]
     */
    public function createHostedCheckout(int $amount, string $redirectUrl, array $references): CreateHostedCheckoutResponse
    {
        $merchantClient = $this->getMerchantClient();
        $hostedCheckoutClient = $merchantClient->hostedCheckout();
        $createHostedCheckoutRequest = new CreateHostedCheckoutRequest();
        
        $cardPaymentMethodSpecificInput = new CardPaymentMethodSpecificInput();
        $cardPaymentMethodSpecificInput->setAuthorizationMode(value: 'SALE');
        $createHostedCheckoutRequest->setCardPaymentMethodSpecificInput($cardPaymentMethodSpecificInput);

        $order = new Order();
        $amountOfMoney = new AmountOfMoney();
        $amountOfMoney->setCurrencyCode(PaymentConstants::CURRENCY_CODE);
        $amountOfMoney->setAmount($amount);
        $order->setAmountOfMoney($amountOfMoney);
        $orderReferences = null;
        if (null !== ($references['descriptor']?? null)) {
            $orderReferences = new OrderReferences();
            $orderReferences->setDescriptor($references['descriptor']);
        }
        if (null !== ($references['merchantReference']?? null)) {
            if (null === $orderReferences) {
                $orderReferences = new OrderReferences();
            }

            $orderReferences->setMerchantReference($references['merchantReference']);
        }
        if (null !== ($references['merchantParameters']?? null)) {
            if (null === $orderReferences) {
                $orderReferences = new OrderReferences();
            }

            $orderReferences->setMerchantParameters($references['merchantParameters']);
        }
        if (null !== $orderReferences) {
            $order->setReferences($orderReferences);
        }

        $createHostedCheckoutRequest->setOrder($order);

        $hostedCheckoutSpecificInput = new HostedCheckoutSpecificInput();
        $hostedCheckoutSpecificInput->setReturnUrl($redirectUrl);
        $hostedCheckoutSpecificInput->setShowResultPage(false);
        $createHostedCheckoutRequest->setHostedCheckoutSpecificInput($hostedCheckoutSpecificInput);

        return $hostedCheckoutClient->createHostedCheckout(body: $createHostedCheckoutRequest);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getCapturesOfPayment(string $paymentID): CapturesResponse
    {
        $merchantClient = $this->getMerchantClient();

        return $merchantClient->payments()->getCaptures(paymentId: $paymentID);
    }

    public function requestCapturePayment(string $paymentID, int $amount): CaptureResponse
    {
        $merchantClient = $this->getMerchantClient();
        $capturePaymentRequest = new CapturePaymentRequest();
        $capturePaymentRequest->setAmount($amount);
        $capturePaymentRequest->setIsFinal(true);

        // Capture payment
        return $merchantClient
            ->payments()
            ->capturePayment(
                paymentId: $paymentID,
                body: $capturePaymentRequest
            );
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPayment(string $paymentID): PaymentResponse
    {
        $merchantClient = $this->getMerchantClient();

        return $merchantClient->payments()->getPayment(paymentId: $paymentID);
    }

    private function getMerchantClient(): MerchantClientInterface
    {
        $connection = new DefaultConnection();
        $communicatorConfiguration = new CommunicatorConfiguration(
            $this->paymentApiKey,
            $this->paymentApiSecret,
            $this->paymentApiEndpoint,
            $this->paymentIntegrator
        );
        $communicator = new Communicator($connection, $communicatorConfiguration);

        $client = new Client($communicator);

        return $client->merchant(merchantId: $this->paymentMerchantId);
    }
}