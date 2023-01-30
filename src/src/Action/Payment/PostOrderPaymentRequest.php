<?php

namespace App\Action\Payment;

use App\Constants\Payment\OrderTypeConstants;
use App\Constants\Payment\OrderWorkflowStateConstants;
use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\User;
use App\Exception\Payment\PaymentAlreadyExist;
use App\Manager\StudentManager;
use App\Service\Payment\PaymentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @method User getUser()
 */
#[AsController]
class PostOrderPaymentRequest extends AbstractController
{
    public function __construct(
        private PaymentManager $paymentManager,
        private StudentManager $studentManager,
        private TranslatorInterface $translator
    ) {}

    public function __invoke(Order $data): JsonResponse
    {
        if (OrderWorkflowStateConstants::STATE_VALIDATED === $data->getState()) {
            throw new PaymentAlreadyExist(type: $this->translator->trans("order.type.{$data->getType()}", ['%session_name%' => $data->getExamSession()?->getExamClassification()->getName()]));
        }

        if (
            OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION === $data->getType() and
            (
                $this->studentManager->hasAlreadyApplyToSessionTheSameDayOnOtherCampus(
                    student: $data->getStudent(),
                    examSession: $data->getExamSession()
                ) or
                $this->studentManager->hasAlreadyApplyToSessionTheSameDayOnSameSessionType(
                    student: $data->getStudent(),
                    examSession: $data->getExamSession(),
                    existing: true
                )
            )
        ) {
            throw new BadRequestHttpException("Vous êtes déjà inscrit à une session le même jour, sélectionner une autre date ou un autre campus ");
        }

        /** @var Payment $payment */
        $payment = $data->getPayments()->filter(function (Payment $p) {
            return PaymentWorkflowStateConstants::STATE_CREATED === $p->getState();
        })->first();

        return $this->json($this->paymentManager->requestPayment(payment: $payment), Response::HTTP_MOVED_PERMANENTLY);
    }
}