<?php

namespace App\Controller\Payment;

use App\Constants\Payment\OrderTypeConstants;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Form\Order\OrderManualType;
use App\Repository\Payment\OrderRepository;
use App\Repository\StudentRepository;
use App\Service\Payment\PaymentManager;
use App\Service\Workflow\Payment\PaymentWorkflowManager;
use App\Service\Workflow\Student\StudentWorkflowManager;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order')]
#[IsGranted('ROLE_COORDINATOR')]
class OrderController extends AbstractController
{
    #[Route('', name: 'order_index', methods: ['POST', 'GET'])]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        OrderRepository $orderRepository,
        StudentRepository $studentRepository
    ): Response
    {
        $criteria = [];
        $identifier = $request->query->get('identifier');
        if (null !== $identifier) {
            $criteria['student.identifier'] = $identifier;
            $student = $studentRepository->findOneBy(['identifier' => $identifier]);
        }
        $pagination = $paginator->paginate(
            $orderRepository->getPaginatorQuery(criteria: $criteria),
            $request->query->getInt('page', 1),
            25,
            [
                PaginatorInterface::DEFAULT_SORT_FIELD_NAME => 'o.type',
                PaginatorInterface::DEFAULT_SORT_DIRECTION => 'desc',
            ]
        );

        $pagination->setCustomParameters(['align' => 'right',]);

        return $this->render('payment/order/index.html.twig', [
            'pagination' => $pagination,
            'student' => $student?? null,
        ]);
    }

    #[Route('/manual/new', name: 'order_manual_new', methods: ['GET', 'POST'])]
    public function manualOrderNew(
        Request $request,
        StudentWorkflowManager $studentWorkflowManager,
        PaymentWorkflowManager $paymentWorkflowManager,
        OrderRepository $orderRepository,
        PaymentManager $paymentManager,
        StudentRepository $studentRepository
    ): Response
    {
        $payment = new Payment();
        $identifier = $request->query->get('identifier');
        if (null !== $identifier) {
            $student = $studentRepository->findOneBy(['identifier' => $identifier]);
        }

        $order = new Order();
        $order->addPayment($payment);
        $order->setStudent($student?? null);
        $form = $this->createForm(OrderManualType::class, $order, ['attr' => ['disabled' => false]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var Order $data */
                $data = $form->getData();
                if ($data->getType() == OrderTypeConstants::SCHOOL_REGISTRATION_FEES) {
                    $data->setExamSession(null);
                }

                $existingOrder = $orderRepository->findOneBy([
                    'type' => $data->getType(),
                    'student' => $data->getStudent(),
                    'examSession' => $data->getExamSession()
                ]);

                if (null !== $existingOrder and !$existingOrder->canRecreate()) {
                    throw new Exception('Les frais ont déjà été réglés, le paiement n\'a pas été créé.');
                }
                if (null === $existingOrder) {
                    $order->setAmount($paymentManager->getPaymentAmount($order));
                    $orderRepository->save($order, true);
                } else {
                    $existingOrder->addPayment($payment);
                    $orderRepository->save($existingOrder, true);
                }

                $paymentWorkflowManager->evolute($payment);

                if ($order->getType() === OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION) {
                    $studentWorkflowManager->valid($order->getStudent());
                }
            } catch (Exception $exception) {
                $this->addFlash('error', $exception->getMessage());
                return $this->redirectToRoute('order_manual_new', ['identifier' => $order->getStudent()->getIdentifier()]);
            }

            return $this->redirectToRoute('order_index', ['identifier' => $order->getStudent()->getIdentifier()]);
        }

        return $this->renderForm('payment/order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'order_show', methods: ['GET'])]
    public function manualOrderShow(Order $order): Response
    {
        return $this->render('payment/order/show.html.twig', ['order' => $order]);
    }
}