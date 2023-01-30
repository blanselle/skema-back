<?php

declare(strict_types=1);

namespace App\Ruler\CV\Rule;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\BacTypeBonusRepository;
use App\Ruler\CV\CVRulerInterface;
use Exception;

class BacTypeRule implements CVRulerInterface
{
    public function __construct(private BacTypeBonusRepository $bacTypeBonusRepository)
    {
    }

    public function getBonus(Student $student): float
    {
        if (null === $student->getCv()?->getBac()) {
            throw new Exception('The Cv is not complete');
        }

        $bacTypeBonuses = $this->bacTypeBonusRepository->findByCategory(BonusNameConstants::BAC_TYPE, $student->getProgramChannel());
        $bonus = 0;
        foreach ($bacTypeBonuses as $bacTypeBonus) {
            foreach ($student->getCv()->getBac()->getBacTypes() as $bacType) {
                if ($bacTypeBonus->getBacType() === $bacType) {
                    $bonus += $bacTypeBonus->getValue();
                }
            }
        }

        return $bonus;
    }
}
