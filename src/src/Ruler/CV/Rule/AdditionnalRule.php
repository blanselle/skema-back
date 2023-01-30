<?php

declare(strict_types=1);

namespace App\Ruler\CV\Rule;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Entity\Student;
use App\Manager\StudentManager;
use App\Repository\Admissibility\Bonus\BasicBonusRepository;
use App\Ruler\CV\CVRulerInterface;

class AdditionnalRule implements CVRulerInterface
{
    public function __construct(
        private BasicBonusRepository $basicBonusRepository,
        private StudentManager $studentManager,
    ) {
    }

    public function getBonus(Student $student): float
    {
        $basicBonuses = $this->basicBonusRepository->findByCategory(
            BonusNameConstants::ADDITIONNAL,
            $student->getProgramChannel()
        );

        // should have only one additionnal bonus for one program channel
        $basicBonus = array_shift($basicBonuses);
        $bonus = 0;
        if ($this->studentManager->hasDualPathStudentDiploma($student)) {
            $bonus = $basicBonus->getValue();
        }

        return $bonus;
    }
}
