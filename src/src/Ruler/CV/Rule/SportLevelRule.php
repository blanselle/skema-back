<?php

declare(strict_types=1);

namespace App\Ruler\CV\Rule;

use App\Constants\Admissibility\Bonus\BonusNameConstants;
use App\Constants\Media\MediaWorflowStateConstants;
use App\Entity\Student;
use App\Repository\Admissibility\Bonus\SportLevelBonusRepository;
use App\Ruler\CV\CVRulerInterface;
use Exception;

class SportLevelRule implements CVRulerInterface
{
    public function __construct(private SportLevelBonusRepository $sportLevelBonusRepository)
    {
    }

    public function getBonus(Student $student): float
    {
        if (null === $student->getAdministrativeRecord()) {
            throw new Exception('The administrative record is not completed');
        }

        $bonuses = $this->sportLevelBonusRepository->findByCategory(BonusNameConstants::SPORT_LEVEL, $student->getProgramChannel());

        $medias = $student->getAdministrativeRecord()->getHighLevelSportsmanMedias();
        if(count($medias) < 1) {
            return 0;
        }
        
        $sportLevelMediaIsVerified = true;
        foreach($medias as $media) {
            if(MediaWorflowStateConstants::STATE_ACCEPTED !== $media->getState()) {
                $sportLevelMediaIsVerified = false;
            }
        }

        foreach ($bonuses as $bonus) {
            if (
                $bonus->getSportLevel() === $student->getAdministrativeRecord()->getSportLevel() &&
                $sportLevelMediaIsVerified
            ) {
                return $bonus->getValue();
            }
        }

        return 0;
    }
}
