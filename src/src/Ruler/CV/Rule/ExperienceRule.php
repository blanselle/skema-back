<?php

declare(strict_types=1);

namespace App\Ruler\CV\Rule;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\CV\Experience\ExperienceStateConstants;
use App\Entity\Admissibility\Bonus\ExperienceBonus;
use App\Entity\CV\Experience;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\ExperienceBonusRepository;
use App\Ruler\CV\CVRulerInterface;
use Exception;

class ExperienceRule implements CVRulerInterface
{
    public function __construct(
        private ExperienceBonusRepository $experienceBonusRepository,
    ) {
    }

    public function getBonus(Student $student): float
    {
        if (null === $student->getCv()) {
            throw new Exception('The Cv is not complete');
        }

        $experienceBonuses = $this->experienceBonusRepository->findByCategory(
            BonusNameConstants::EXPERIENCE,
            $student->getProgramChannel()
        );

        $groupedExperiencesDurations = [];
        /** @var Experience $experience */
        foreach ($student->getCv()->getExperiences() as $experience) {
            if (ExperienceStateConstants::STATE_REJECTED === $experience->getState()) {
                continue;
            }

            if (!array_key_exists($experience->getExperienceType(), $groupedExperiencesDurations)) {
                $groupedExperiencesDurations[$experience->getExperienceType()] = $experience->getDuration();
            } else {
                $groupedExperiencesDurations[$experience->getExperienceType()] += $experience->getDuration();
            }
        }

        $bonus = 0;
        foreach ($groupedExperiencesDurations as $experienceType => $groupedExperiencesDuration) {
            /** @var ExperienceBonus $experienceBonus */
            foreach ($experienceBonuses as $experienceBonus) {

                if (
                    $experienceBonus->getType() === $experienceType &&
                    $experienceBonus->getDuration() <= $groupedExperiencesDuration
                ) {
                    $bonus += $experienceBonus->getValue();
                    break;
                }
            }
        }

        return $bonus;
    }
}
