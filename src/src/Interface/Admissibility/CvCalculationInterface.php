<?php

declare(strict_types=1);

namespace App\Interface\Admissibility;

use App\Entity\CV\Cv;

/**
 * L'entité qui implemente cette interface relancera automatiquement le calcul de la note et du bonus du Cv à la persistance de celle-ci
 */
interface CvCalculationInterface
{
    public function getCv(): ?Cv;
}
