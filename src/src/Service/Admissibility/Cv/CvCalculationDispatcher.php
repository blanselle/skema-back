<?php

declare(strict_types=1);

namespace App\Service\Admissibility\Cv;

use App\Entity\CV\Cv;
use App\Message\CvCalculationMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class CvCalculationDispatcher
{
    public function __construct(
        private MessageBusInterface $bus,
    ) {
        
    }

    public function dispatch(Cv $cv): void
    {
        $this->bus->dispatch((new CvCalculationMessage($cv->getId())));
    }
}
