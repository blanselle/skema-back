<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Constants\Admissibility\Bonus\BonusInfoLabelConstants;
use App\Entity\AdministrativeRecord\SportLevel;
use App\Entity\Traits\BonusTrait;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\SportLevelBonusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SportLevelBonusRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_sport_level')]
class SportLevelBonus implements BonusInterface
{
    use BonusTrait;

    #[ORM\ManyToOne(targetEntity: SportLevel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private SportLevel $sportLevel;

    public function getSportLevel(): SportLevel
    {
        return $this->sportLevel;
    }

    public function setSportLevel(SportLevel $sportLevel): self
    {
        $this->sportLevel = $sportLevel;

        return $this;
    }

    public function getInfos(): array
    {
        return [
            BonusInfoLabelConstants::LEVEL_LABEL => $this->getSportLevel()->getLabel(),
        ];
    }
}
