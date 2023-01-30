<?php

namespace App\Service\Workflow\Payment;

use App\Constants\Payment\PaymentModeConstants;
use App\Constants\Payment\PaymentWorkflowTransitionConstants;
use App\Entity\Payment\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

class PaymentWorkflowManager
{
    public function __construct(
        private Registry $workflowRegistry,
        private EntityManagerInterface $entityManager,
        private OrderWorkflowManager $orderWorkflowManager,
    ) {}

    public function evolute(Payment $payment): void
    {
        $workflow = $this->workflowRegistry->get($payment, 'payment');
        $externalState = $payment->getExternalStatus();

        if (in_array($externalState, PaymentWorkflowTransitionConstants::TO_TREATED, true)) {
            $this->treated(workflow: $workflow, payment: $payment);
        } elseif (in_array($externalState, PaymentWorkflowTransitionConstants::TO_VALIDATE, true)) {
            $this->validate(workflow: $workflow, payment: $payment);
        } elseif (in_array($externalState, PaymentWorkflowTransitionConstants::TO_CANCEL, true)) {
            $this->cancel(workflow: $workflow, payment: $payment);
        } elseif (in_array($externalState, PaymentWorkflowTransitionConstants::TO_REJECT, true)) {
            $this->reject(workflow: $workflow, payment: $payment);
        } elseif (PaymentModeConstants::PAYMENT_MODE_ONLINE !== $payment->getMode() && null === $externalState) {
            $this->treated(workflow: $workflow, payment: $payment);
            $this->validate(workflow: $workflow, payment: $payment);
        }
    }

    public function forceCancelPayment(Payment $payment): void
    {
        $workflow = $this->workflowRegistry->get($payment, 'payment');
        $this->cancel(workflow: $workflow, payment: $payment);
    }

    private function treated(Workflow $workflow, Payment $payment): void
    {
        if ($workflow->can($payment, PaymentWorkflowTransitionConstants::TRANSITION_TREATED)) {
            $workflow->apply($payment, PaymentWorkflowTransitionConstants::TRANSITION_TREATED);
            $this->entityManager->flush();

            $this->orderWorkflowManager->treated(order: $payment->getIndent());
        }
    }

    private function validate(Workflow $workflow, Payment $payment): void
    {
        if ($workflow->can($payment, PaymentWorkflowTransitionConstants::TRANSITION_VALIDATE)) {
            $workflow->apply($payment, PaymentWorkflowTransitionConstants::TRANSITION_VALIDATE);
            $this->entityManager->flush();

            $this->orderWorkflowManager->validate(order: $payment->getIndent());
        }
    }

    private function reject(Workflow $workflow, Payment $payment): void
    {
        if ($workflow->can($payment, PaymentWorkflowTransitionConstants::TRANSITION_REJECT)) {
            $workflow->apply($payment, PaymentWorkflowTransitionConstants::TRANSITION_REJECT);
            $this->entityManager->flush();

            $this->orderWorkflowManager->recreate(order: $payment->getIndent());
        }
    }

    private function cancel(Workflow $workflow, Payment $payment): void
    {
        if ($workflow->can($payment, PaymentWorkflowTransitionConstants::TRANSITION_CANCEL)) {
            $workflow->apply($payment, PaymentWorkflowTransitionConstants::TRANSITION_CANCEL);
            $this->entityManager->flush();

            $this->orderWorkflowManager->recreate(order: $payment->getIndent());
        }
    }
}