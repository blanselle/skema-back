<?php

declare(strict_types=1);

namespace App\Manager\Admissibility;

use App\Entity\Admissibility\Param;

class ParamManager
{
    public function checkBordersConsistency(Param $param): bool
    {
        $previousScore = null;
        $previousNote = null;
        foreach ($param->getBorders()->toArray() as $orderedBorder) {
            if (null === $previousScore) {
                $previousScore = $orderedBorder->getScore();
                $previousNote = $orderedBorder->getNote();
                continue;
            }

            if ($previousNote >= $orderedBorder->getNote() || $previousScore >= $orderedBorder->getScore()) {
                return false;
            }

            $previousScore = $orderedBorder->getScore();
            $previousNote = $orderedBorder->getNote();
        }

        return true;
    }
}
