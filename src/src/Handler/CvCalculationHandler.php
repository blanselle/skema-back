<?php

declare(strict_types=1);

namespace App\Handler;

use App\Exception\Messenger\MessengerRessourceNotFound;
use App\Message\CvCalculationMessage;
use App\Repository\CV\CvRepository;
use App\Service\Admissibility\Cv\CvCalculator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CvCalculationHandler implements MessageHandlerInterface
{
    public function __construct(
        private CvRepository $cvRepository,
        private CvCalculator $cvCalculator,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(CvCalculationMessage $cvCalculationMessage): void
    {
        $cv = $this->cvRepository->findOneBy(['id' => $cvCalculationMessage->getCvId()], []);

        if(null === $cv) {
            throw new MessengerRessourceNotFound("The cv with {$cvCalculationMessage->getCvId()} is not found");
        }

        $this->cvCalculator->updateCvNotes($cv);
        $this->em->flush();
    }
}
