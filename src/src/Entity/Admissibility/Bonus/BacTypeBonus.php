<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Constants\Admissibility\Bonus\BonusInfoLabelConstants;
use App\Entity\CV\Bac\BacType;
use App\Entity\Traits\BonusTrait;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\BacTypeBonusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BacTypeBonusRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_bac_type')]
class BacTypeBonus implements BonusInterface
{
    use BonusTrait;

    #[ORM\ManyToOne(targetEntity: BacType::class)]
    #[ORM\JoinColumn(nullable: false)]
    private BacType $bacType;

    public function getBacType(): BacType
    {
        return $this->bacType;
    }

    public function setBacType(BacType $bacType): self
    {
        $this->bacType = $bacType;

        return $this;
    }

    public function getInfos(): array
    {
        return [
            BonusInfoLabelConstants::BAC_TYPE_LABEL => $this->getBacType()->getName(),
        ];
    }
}
