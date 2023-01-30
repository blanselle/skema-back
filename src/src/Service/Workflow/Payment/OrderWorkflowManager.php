<?php

namespace App\Service\Workflow\Payment;

use App\Constants\Payment\OrderWorkflowTransitionConstants;
use App\Entity\Payment\Order;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Workflow;

class OrderWorkflowManager
{
    public function __construct(
        private Registry $workflowRegistry,
        private StudentWorkflowManager $studentWorkflowManager,
        private EntityManagerInterface $entityManager
    ) {}

    public function treated(Order $order): void
    {
        $workflow = $this->getWorkflow(order: $order);

        if ($workflow->can($order, OrderWorkflowTransitionConstants::TRANSITION_TREATED)) {
            $workflow->apply($order, OrderWorkflowTransitionConstants::TRANSITION_TREATED);
            $this->entityManager->flush();
        }
    }

    public function validate(Order $order): void
    {
        $workflow = $this->getWorkflow(order: $order);

        if ($workflow->can($order, OrderWorkflowTransitionConstants::TRANSITION_VALIDATE)) {
            $workflow->apply($order, OrderWorkflowTransitionConstants::TRANSITION_VALIDATE);
            $this->entityManager->flush();

            $this->studentWorkflowManager->valid(student: $order->getStudent());
        }
    }

    public function recreate(Order $order): void
    {
        $workflow = $this->getWorkflow(order: $order);

        if (
            $order->canRecreate() and
            $workflow->can($order, OrderWorkflowTransitionConstants::TRANSITION_RECREATE)
        ) {
            $workflow->apply($order, OrderWorkflowTransitionConstants::TRANSITION_RECREATE);
            $this->entityManager->flush();
        }
    }

    private function getWorkflow(Order $order): Workflow
    {
        return $this->workflowRegistry->get(subject: $order, workflowName: 'order');
    }
}