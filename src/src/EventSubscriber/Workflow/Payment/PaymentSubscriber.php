<?php

namespace App\EventSubscriber\Workflow\Payment;

use App\Constants\Notification\NotificationConstants;
use App\Constants\Notification\NotificationExemptionMessageConstants;
use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\PaymentWorkflowTransitionConstants;
use App\Entity\Payment\Payment;
use App\Manager\NotificationManager;
use App\Service\Notification\NotificationCenter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;

class PaymentSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private NotificationCenter $notificationCenter,
        private NotificationManager $notificationManager,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            sprintf(
                'workflow.payment.transition.%s',
                PaymentWorkflowTransitionConstants::TRANSITION_VALIDATE
            ) => 'validate',
            sprintf(
                'workflow.payment.transition.%s',
                PaymentWorkflowTransitionConstants::TRANSITION_REJECT
            ) => 'reject',
            sprintf(
                'workflow.payment.transition.%s',
                PaymentWorkflowTransitionConstants::TRANSITION_CANCEL
            ) => 'cancel',
        ];
    }

    public function validate(TransitionEvent $event): void
    {
        $payment = $event->getSubject();
        if (!$payment instanceof Payment) {
            return;
        }

        $label = $this->getLabel(payment: $payment);

        $content = match($payment->getIndent()->getType()) {
            OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION => '',
            default => 'Vous pouvez poursuivre votre inscription en complétant votre CV.'
        };

        $this->notificationCenter->dispatch(
            $this->notificationManager->createNotification(
                receiver: $payment->getIndent()->getStudent()->getUser(),
                blocKey: NotificationExemptionMessageConstants::NOTIFICATION_PAYMENT_VALIDATED,
                params: [
                    'label' => $label,
                    'content' => $content,
                ]
            ),
            [NotificationConstants::TRANSPORT_DB]
        );
    }

    public function reject(TransitionEvent $event): void
    {
        $payment = $event->getSubject();
        if (!$payment instanceof Payment) {
            return;
        }

        $label = $this->getLabel(payment: $payment);

        $this->notificationCenter->dispatch(
            $this->notificationManager->createNotification(
                receiver: $payment->getIndent()->getStudent()->getUser(),
                blocKey: NotificationExemptionMessageConstants::NOTIFICATION_PAYMENT_REJECTED,
                params: [
                    'label' => $label,
                ]
            ),
            [NotificationConstants::TRANSPORT_DB]
        );
    }

    public function cancel(TransitionEvent $event): void
    {
        $payment = $event->getSubject();
        if (!$payment instanceof Payment) {
            return;
        }

        $label = $this->getLabel(payment: $payment);

        $this->notificationCenter->dispatch(
            $this->notificationManager->createNotification(
                receiver: $payment->getIndent()->getStudent()->getUser(),
                blocKey: NotificationExemptionMessageConstants::NOTIFICATION_PAYMENT_CANCELED,
                params: [
                    'label' => $label,
                ]
            ),
            [NotificationConstants::TRANSPORT_DB]
        );
    }

    private function getLabel(Payment $payment): string
    {
        return match($payment->getIndent()->getType()) {
            OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION => "frais d'inscription à la session {$payment->getIndent()->getExamSession()->getExamClassification()->getName()}",
            default => 'frais de concours'
        };
    }
}