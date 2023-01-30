<?php

declare(strict_types=1);

namespace App\Ruler\CV\Rule;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\LanguageBonusRepository;
use App\Ruler\CV\CVRulerInterface;
use Exception;

class LanguageRule implements CVRulerInterface
{
    public function __construct(
        private LanguageBonusRepository $languageBonusRepository,
    ) {
    }

    public function getBonus(Student $student): float
    {
        if (null === $student->getCv()) {
            throw new Exception('The cv is not completed');
        }

        $bonuses = $this->languageBonusRepository->findByCategory(
            BonusNameConstants::LANGUAGE,
            $student->getProgramChannel()
        );

        $nbLanguages = count($student->getCv()->getLanguages());

        foreach ($bonuses as $bonus) {
            if ($nbLanguages >= $bonus->getMin()) {
                return $bonus->getValue();
            }
        }

        return 0;
    }
}
