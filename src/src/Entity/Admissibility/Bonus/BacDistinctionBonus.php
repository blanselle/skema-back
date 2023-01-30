<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Constants\Admissibility\Bonus\BonusInfoLabelConstants;
use App\Entity\CV\Bac\BacDistinction;
use App\Entity\Traits\BonusTrait;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\BacDistinctionBonusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BacDistinctionBonusRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_distinction')]
class BacDistinctionBonus implements BonusInterface
{
    use BonusTrait;

    #[ORM\ManyToOne(targetEntity: BacDistinction::class)]
    private BacDistinction $bacDistinction;

    public function getBacDistinction(): BacDistinction
    {
        return $this->bacDistinction;
    }

    public function setBacDistinction(BacDistinction $bacDistinction): self
    {
        $this->bacDistinction = $bacDistinction;

        return $this;
    }

    public function getInfos(): array
    {
        return [
            BonusInfoLabelConstants::DISTINCTION_LABEL => $this->getBacDistinction()->getLabel(),
        ];
    }
}
