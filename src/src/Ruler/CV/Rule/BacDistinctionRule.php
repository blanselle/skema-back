<?php

declare(strict_types=1);

namespace App\Ruler\CV\Rule;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\BacDistinctionBonusRepository;
use App\Ruler\CV\CVRulerInterface;
use Exception;

class BacDistinctionRule implements CVRulerInterface
{
    public function __construct(private BacDistinctionBonusRepository $bacDistinctionBonusRepository)
    {
    }

    public function getBonus(Student $student): float
    {
        if (null === $student->getCv()?->getBac()) {
            throw new Exception('The Cv is not complete');
        }

        if (null === $student->getCv()->getBac()->getMedia()) {
            return 0;
        }

        $bonuses = $this->bacDistinctionBonusRepository->findByCategory(BonusNameConstants::BAC_DISTINCTION, $student->getProgramChannel());

        foreach ($bonuses as $bonus) {

            if (
                $bonus->getBacDistinction() === $student->getCv()->getBac()->getBacDistinction() &&
                MediaWorflowStateConstants::STATE_ACCEPTED === $student->getCv()->getBac()->getMedia()->getState()
            ) {
                return $bonus->getValue();
            }
        }

        return 0;
    }
}
