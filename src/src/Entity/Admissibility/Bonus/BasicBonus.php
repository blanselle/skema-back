<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Entity\Traits\BonusTrait;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\BasicBonusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasicBonusRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_basic')]
class BasicBonus implements BonusInterface
{
    use BonusTrait;

    public function getInfos(): array
    {
        return [];
    }
}
