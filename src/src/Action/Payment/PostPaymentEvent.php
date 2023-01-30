<?php

namespace App\Action\Payment;

use App\Service\Payment\PaymentManager;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
#[Route('/api/payments/event', name: 'payments_event', requirements: ['token' => '.+'], methods: ['POST'])]
class PostPaymentEvent extends AbstractController
{
    public function __construct(
        private PaymentManager $paymentManager,
        private LoggerInterface $paymentLogger,
        #[Autowire('%payment_webhook_token%')]
        private string $paymentWebhookToken
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->query->get('token');
        if ($this->paymentWebhookToken !== $token) {
            throw new BadRequestHttpException("Invalid token");
        }

        $payload = $request->getContent();

        $this->paymentLogger->info("Receive event with payload {$payload}");

        $transaction = json_decode($payload);

        $this->paymentManager->updatePaymentsFromWebhooksEvent(transaction: $transaction);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}