<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Constants\Payment\OrderTypeConstants;
use App\Entity\Payment\Order;
use App\Entity\Payment\Payment;
use App\Entity\User;
use App\Repository\Payment\OrderRepository;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Security\Core\Security;

class OrderPaymentRequestInputDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private OrderRepository $orderRepository,
        private Security $security,
    ) {}

    /**
     * @param mixed $object
     * @param string $to
     * @param array $context
     * @return object
     */
    public function transform($object, string $to, array $context = [])
    {
        if ($object->type === OrderTypeConstants::REGISTRATION_FEE_FOR_EXAM_SESSION && null === $object->examSession) {
            throw new TransformationFailedException("Exam Student missing for type {$object->type}");
        }

        /** @var User $user */
        $user = $this->security->getUser();

        $payment = new Payment();
        $payment->setRedirectUrl($object->redirectUrl);

        // In case of error receive by PSP or payment rejected, the order already exist in database
        $order = $this->orderRepository->findOneBy([
            'type' => $object->type,
            'student' => $user->getStudent(),
            'examSession' => $object->examSession,
        ]);

        if (null === $order) {
            $order = new Order();
            $order->setType($object->type);
            $order->setStudent($user->getStudent());
            $order->setExamSession($object->examSession);
        }

        $order->addPayment($payment);

        return $order;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Order) {
            return false;
        }

        return Order::class === $to && null !== ($context['input']['class']?? null);
    }
}