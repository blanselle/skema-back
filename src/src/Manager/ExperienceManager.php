<?php

declare(strict_types=1);

namespace App\Manager;

use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Constants\CV\Experience\TimeTypeConstants;
use App\Entity\CV\Experience;

class ExperienceManager
{
    public function getDurationForExperience(Experience $experience): int
    {
        if ($experience->getTimeType() === TimeTypeConstants::PARTIAL_TIME) {
            return $this->getDurationForPartialTimeExperience($experience);
        }

        return $this->getDurationForFullTimeExperience($experience);
    }

    private function getDurationForPartialTimeExperience(Experience $experience): int
    {
        $daysCount = (int) $this->getDiffDateIntervalFromExperience($experience)->format("%a");

        $durationInMounth = round(($daysCount / 30) * ($experience->getHoursPerWeek() / 35) / 5, 1) * 5;

        return match($experience->getExperienceType()) {
            ExperienceTypeConstants::TYPE_ASSOCIATIVE => (int) round($durationInMounth / 12),
            default => (int) round($durationInMounth)
        };
    }

    private function getDurationForFullTimeExperience(Experience $experience): int
    {        
        $diff =  $this->getDiffDateIntervalFromExperience($experience);

        $durationInMounth = round($diff->y * 12 + $diff->m + $diff->d / 30);

        return match($experience->getExperienceType()) {
            ExperienceTypeConstants::TYPE_ASSOCIATIVE => (int) round($durationInMounth / 12),
            default => (int) round($durationInMounth)
        };
    }

    private function getDiffDateIntervalFromExperience(Experience $experience): \DateInterval
    {
        return $experience->getEndAt()->diff($experience->getBeginAt());
    }

    public function unActiveBooleanExperienceInCv(Experience $experience): void
    {
        if ($experience->getExperienceType() === ExperienceTypeConstants::TYPE_ASSOCIATIVE) {
            $experience->getCv()->setNoAssociativeExperience(false);
            return;
        }

        if ($experience->getExperienceType() === ExperienceTypeConstants::TYPE_INTERNATIONAL) {
            $experience->getCv()->setNoInternationnalExperience(false);
            return;
        }

        if ($experience->getExperienceType() === ExperienceTypeConstants::TYPE_PROFESSIONAL) {
            $experience->getCv()->setNoProfessionalExperience(false);
            return;
        }
    }

    public function getDurationLabelForExperience(string $experienceType): array
    {
        if ($experienceType === ExperienceTypeConstants::TYPE_PROFESSIONAL || $experienceType === ExperienceTypeConstants::TYPE_INTERNATIONAL) {
            return ['label' => 'Durée en mois', 'required' => false, 'label_attr' => ['class' => 'duration_label']];
        } elseif ($experienceType === ExperienceTypeConstants::TYPE_ASSOCIATIVE) {
            return ['label' => 'Durée en année', 'required' => false, 'label_attr' => ['class' => 'duration_label']];
        }

        return [];
    }
}
