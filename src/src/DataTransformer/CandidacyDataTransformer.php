<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CandidacyOutput;
use App\Entity\Student;
use App\Manager\CandidacyManager;
use App\Message\PaymentsStatusMessage;
use App\Repository\Payment\PaymentRepository;
use Symfony\Component\Messenger\MessageBusInterface;

final class CandidacyDataTransformer implements DataTransformerInterface
{
    public function __construct(
        private CandidacyManager $candidacyManager,
        private PaymentRepository $paymentRepository,
        private MessageBusInterface $bus
    ) {}
    
    public function transform($object, string $to, array $context = []): object
    {
        /** @var Student $object */

        // When payment is cancelled or Invalid or Incomplete no webhook event sent, so we need to update manually the payment state
        $payments = $this->paymentRepository->fetchCreatedPayment(student: $object);
        if (!empty($payments)) {
            $ids = array_map(function($payment) { return $payment->getId(); }, $payments);
            $this->bus->dispatch(new PaymentsStatusMessage(paymentsId: $ids));
        }

        $output = new CandidacyOutput();

        $output->administrativeRecord = $this->candidacyManager->administrativeRecord($object);
        $output->competitionFeesPayment = $this->candidacyManager->schoolRegistration($object);
        $output->cv = $this->candidacyManager->cv($object);
        $output->writtenExamination = $this->candidacyManager->writtenExamination($object);
        $output->hasScholarShipMedia = $this->candidacyManager->hasScholarShipMedia($object);
        $output->hasScholarReportMedia = $this->candidacyManager->hasScholarReportMedia($object);
        $output->hasScore = $this->candidacyManager->hasScore($object);
        
        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return CandidacyOutput::class === $to && $data instanceof Student;
    }
}
