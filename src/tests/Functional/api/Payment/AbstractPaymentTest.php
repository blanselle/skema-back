<?php

namespace App\Tests\Functional\api\Payment;

use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\Student;
use App\Entity\User;
use App\Helper\PaymentHelper;
use App\Repository\UserRepository;
use App\Service\Workflow\Payment\PaymentWorkflowManager;
use App\Tests\Functional\api\AbstractAuthenticatedTest;
use OnlinePayments\Sdk\Domain\CreateHostedCheckoutResponse;
use PHPUnit\Framework\MockObject\MockObject;

abstract class AbstractPaymentTest extends AbstractAuthenticatedTest
{
    protected const REDIRECT_URl = 'https://payment.preprod.direct.worldline-solutions.com/hostedcheckout/PaymentMethods/Selection/2278f7d9d46044dcb4bc575e65b65940';

    protected string $tokenPaymentEvent;
    protected PaymentWorkflowManager $paymentWorkflowManager;
    protected UserRepository $userRepository;
    protected PaymentHelper|MockObject $paymentHelper;

    public function setUp(): void
    {
        parent::setUp();
        $this->tokenPaymentEvent = $this->client->getContainer()->getParameter('payment_webhook_token');
        $this->paymentWorkflowManager = $this->client->getContainer()->get(PaymentWorkflowManager::class);
        $this->userRepository = $this->em->getRepository(User::class);
        $this->paymentHelper = $this->createMock(PaymentHelper::class);
    }

    protected function clearOrder(Order $order): void
    {
        $o = $this->em->getRepository(Order::class)->find($order->getId());
        $payments = $this->em->getRepository(Payment::class)->findBy(['indent' => $order]);
        foreach ($payments as $payment) {
            $o->removePayment($payment);
            $this->em->remove($payment);
        }
        $this->em->remove($o);
        $this->em->flush();
    }

    protected function initPaymentHelperMock(Student $student, string $method, mixed $response): void
    {
        $this->paymentHelper
            ->expects(self::any())
            ->method($method)
            ->with(self::anything())
            ->willReturn($response);
        $this->client->getContainer()->set(PaymentHelper::class, $this->paymentHelper);
    }

    protected function getHostedResponse(string $hostedCheckoutId): CreateHostedCheckoutResponse
    {
        $response = new CreateHostedCheckoutResponse();
        $response->setRETURNMAC('fecab85c-9b0e-42ee-a9d9-ebb69b0c2eb0');
        $response->setHostedCheckoutId($hostedCheckoutId);
        $response->setMerchantReference("order_{$hostedCheckoutId}");
        $response->setRedirectUrl(self::REDIRECT_URl);

        return $response;
    }
}