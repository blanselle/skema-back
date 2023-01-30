<?php

namespace App\Tests\Functional\api\Payment;

use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\Payment\PaymentExternalStatusConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Entity\Payment\Order;
use App\Entity\Student;
use Symfony\Component\HttpFoundation\Response;

class PostOrderPaymentRequestTest extends AbstractPaymentTest
{
    public function testRequestPaymentForRegistrationOk(): void
    {
        $token = $this->getToken([
            'email' => 'candidate.ast1@skema.fr',
            'password' => 'mdp',
        ]);
        /** @var Student $student */
        $student = $this->userRepository->findOneBy(['email' => 'candidate.ast1@skema.fr'])->getStudent();
        $this->createClientWithCredentials($token);

        $this->rebootKernel();

        $this->initPaymentHelperMock(student: $student, method: 'createHostedCheckout', response: $this->getHostedResponse(hostedCheckoutId: $student->getIdentifier()));

        $crawler = $this->client->request(
            'POST',
            'api/orders/payment/request',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'type' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                    'redirectUrl' => 'https://frontend-skema.pictime-groupe-integ.com/',
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_MOVED_PERMANENTLY);

        $order = $this->em->getRepository(Order::class)->findOneBy(['student' => $student, 'type' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES]);
        $this->assertEquals(OrderWorkflowStateConstants::STATE_IN_PROGRESS, $order?->getState());

        $this->clearOrder(order: $order);
    }

    public function testRequestPaymentWithOnePaymentRejected(): void
    {
        $token = $this->getToken([
            'email' => 'candidate.ast1@skema.fr',
            'password' => 'mdp',
        ]);
        /** @var Student $student */
        $student = $this->userRepository->findOneBy(['email' => 'candidate.ast1@skema.fr'])->getStudent();
        $this->createClientWithCredentials($token);

        $this->rebootKernel();

        $this->initPaymentHelperMock(student: $student, method: 'createHostedCheckout', response: $this->getHostedResponse(hostedCheckoutId: $student->getIdentifier()));

        $crawler = $this->client->request(
            'POST',
            'api/orders/payment/request',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'type' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                    'redirectUrl' => 'https://frontend-skema.pictime-groupe-integ.com/',
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_MOVED_PERMANENTLY);

        $order = $this->em->getRepository(Order::class)->findOneBy(['student' => $student, 'type' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES]);
        $this->assertEquals(OrderWorkflowStateConstants::STATE_IN_PROGRESS, $order?->getState());

        $payment = $order->getPayments()->first();
        $payment->setExternalStatus(PaymentExternalStatusConstants::EXTERNAL_STATE_REJECTED);
        $this->em->flush($payment);
        $this->paymentWorkflowManager->evolute(payment: $payment);

        $this->assertSame(PaymentWorkflowStateConstants::STATE_REJECTED, $payment->getState());
        $this->assertEquals(OrderWorkflowStateConstants::STATE_CREATED, $order->getState());

        $response = $this->client->request(
            'POST',
            'api/orders/payment/request',
            [
                'headers' => [
                    'accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'type' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES,
                    'redirectUrl' => 'https://frontend-skema.pictime-groupe-integ.com/',
                ],
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_MOVED_PERMANENTLY);

        $order = $this->em->getRepository(Order::class)->findOneBy(['student' => $student, 'type' => OrderTypeConstants::SCHOOL_REGISTRATION_FEES]);

        $this->assertEquals(OrderWorkflowStateConstants::STATE_IN_PROGRESS, $order->getState());
        $this->assertCount(2, $order->getPayments());
        $this->assertEquals(PaymentWorkflowStateConstants::STATE_CREATED, $order->getPayments()->last()->getState());

        $this->clearOrder(order: $order);
    }
}