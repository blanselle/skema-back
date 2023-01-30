<?php

namespace App\Controller\Payment;

use App\Entity\Payment\Payment;
use App\Exception\Payment\PaymentInvalidArgumentException;
use App\Exception\Payment\PaymentResponseException;
use App\Service\Payment\PaymentManager;
use App\Service\Workflow\Payment\PaymentWorkflowManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/payments')]
#[IsGranted('ROLE_COORDINATOR')]
class PaymentController extends AbstractController
{
    #[Route('/{id}/refresh-state', name: 'payments_refresh_state', methods: ['POST'])]
    public function refreshState(
        Request $request,
        Payment $payment,
        PaymentManager $paymentManager,
        LoggerInterface $paymentLogger
    ): Response
    {
        if ($this->isCsrfTokenValid('refresh-state-'.$payment->getId(), strval($request->request->get('_token')))) {
            try {
                $payment = $paymentManager->requestPaymentStatus(payment: $payment);
                $this->addFlash(type: 'info', message: 'Le statut du payment a été mis à jour avec succès.');
            } catch (PaymentResponseException|PaymentInvalidArgumentException $e) {
                $paymentLogger->error(message: $e->getMessage());
                $this->addFlash(type: 'error', message: $e->getMessage());
            } catch (\Exception $e) {
                $paymentLogger->error(message: $e->getMessage());
                $this->addFlash(type: 'error', message: 'Une erreur est survenue. Contactez votre administrateur.');
            }
        }

        return $this->redirectToRoute('order_show', ['id' => $payment->getIndent()->getId()]);
    }

    #[Route('/{id}/cancel', name: 'payments_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function cancelPayment(
        Request $request,
        Payment $payment,
        PaymentWorkflowManager $paymentWorkflowManager,
        LoggerInterface $paymentLogger
    ): Response
    {
        if ($this->isCsrfTokenValid('cancel-'.$payment->getId(), strval($request->request->get('_token')))) {
            try {
                $paymentWorkflowManager->forceCancelPayment(payment: $payment);
                $this->addFlash(type: 'info', message: 'Le paiement a été annulé.');
            } catch (\Exception $e) {
                $paymentLogger->error(message: $e->getMessage());

                $this->addFlash(type: 'error', message: $e->getMessage());
            }
        }

        return $this->redirectToRoute('order_show', ['id' => $payment->getIndent()->getId()]);
    }
}