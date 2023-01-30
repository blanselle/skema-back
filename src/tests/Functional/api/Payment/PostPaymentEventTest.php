<?php

namespace App\Tests\Functional\api\Payment;

use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\Payment\PaymentExternalStatusConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\Student;

class PostPaymentEventTest extends AbstractPaymentTest
{
    public function testPaymentEvent(): void
    {
        /** @var Student $student */
        $student = $this->userRepository->findOneBy(['email' => 'candidate.ast1@skema.fr'])->getStudent();
        $order = (new Order())
            ->setAmount(1000)
            ->setState(OrderWorkflowStateConstants::STATE_IN_PROGRESS)
            ->setStudent($student)
            ->setType(OrderTypeConstants::SCHOOL_REGISTRATION_FEES)
            ->addPayment(
                (new Payment())
                ->setState(PaymentWorkflowStateConstants::STATE_IN_PROGRESS)
                ->setExternalStatus(PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURE_REQUESTED)
                ->setRedirectUrl(self::REDIRECT_URl)
                ->setExternalPaymentID($student->getIdentifier())
                ->setExternalHostedCheckoutId("order_{$student->getIdentifier()}")
            );

        $this->em->getRepository(Order::class)->save($order, true);

        $crawler = $this->client->request('POST', '/api/payments/event', [
            'query' => ['token' => $this->tokenPaymentEvent],
            'json' => $this->getPaymentEventPayload(
                type: OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                merchantReference: $order->getPayments()->first()->getMerchantReference(),
                hostedCheckoutId: "order_{$student->getIdentifier()}",
                externalStatus: PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURED,
                amount: 1000
            )
        ]);

        $this->assertEquals(OrderWorkflowStateConstants::STATE_VALIDATED, $order->getState());

        $this->clearOrder(order: $order);
    }

    public function testPaymentEventWithoutOrBadToken(): void
    {
        /** @var Student $student */
        $student = $this->userRepository->findOneBy(['email' => 'candidate.ast1@skema.fr'])->getStudent();
        $order = (new Order())
            ->setAmount(1000)
            ->setState(OrderWorkflowStateConstants::STATE_IN_PROGRESS)
            ->setStudent($student)
            ->setType(OrderTypeConstants::SCHOOL_REGISTRATION_FEES)
            ->addPayment(
                (new Payment())
                    ->setState(PaymentWorkflowStateConstants::STATE_IN_PROGRESS)
                    ->setExternalStatus(PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURE_REQUESTED)
                    ->setRedirectUrl(self::REDIRECT_URl)
                    ->setExternalPaymentID($student->getIdentifier())
                    ->setExternalHostedCheckoutId("order_{$student->getIdentifier()}")
            );

        $crawler = $this->client->request('POST', '/api/payments/event', [
            'json' => $this->getPaymentEventPayload(
                type: OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                merchantReference: $order->getPayments()->first()->getMerchantReference(),
                hostedCheckoutId: "order_{$student->getIdentifier()}",
                externalStatus: PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURED,
                amount: 1000
            )
        ]);

        $this->assertResponseStatusCodeSame(400);

        $crawler = $this->client->request('POST', '/api/payments/event', [
            'query' => ['token' => 'bad-token'],
            'json' => $this->getPaymentEventPayload(
                type: OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                merchantReference: $order->getPayments()->first()->getMerchantReference(),
                hostedCheckoutId: "order_{$student->getIdentifier()}",
                externalStatus: PaymentExternalStatusConstants::EXTERNAL_STATE_CAPTURED,
                amount: 1000
            )
        ]);

        $this->assertResponseStatusCodeSame(400);
    }

    private function getPaymentEventPayload(string $type, string $merchantReference, string $hostedCheckoutId, string $externalStatus, int $amount): mixed
    {
        $payload = '{"apiVersion":"v1","created":"2020-12-09T11:20:40.3744722+01:00","id":"34b8a607-1fce-4003-b3ae-a4d29e92b232","merchantId":"SKEMAAST","payment":{"paymentOutput":{"amountOfMoney":{"amount":AMOUNT,"currencyCode":"EUR"},"references":{"merchantReference":"MERCHANT_REFERENCE"},"cardPaymentMethodSpecificOutput":{"paymentProductId":1,"card":{"cardNumber":"************1111","expiryDate":"0122"},"fraudResults":{"fraudServiceResult":"no-advice"},"threeDSecureResults":{"eci":"9"}},"paymentMethod":"card"},"status":"STATUS","statusOutput":{"isCancellable":false,"statusCategory":"CREATED","statusCode":0,"isAuthorized":false,"isRefundable":false},"id":"EXTERNAL_PAYMENT_ID"},"type":"payment.created"}';

        $payload = str_replace('TYPE', $type, $payload);
        $payload = str_replace('MERCHANT_REFERENCE', $merchantReference, $payload);
        $payload = str_replace('EXTERNAL_PAYMENT_ID', $hostedCheckoutId, $payload);
        $payload = str_replace('STATUS', $externalStatus, $payload);
        $payload = str_replace('AMOUNT', $amount, $payload);

        return json_decode($payload);
    }
}