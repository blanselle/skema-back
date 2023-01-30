<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Constants\Admissibility\Bonus\BonusInfoLabelConstants;
use App\Entity\Traits\BonusTrait;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\LanguageBonusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LanguageBonusRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_language')]
class LanguageBonus implements BonusInterface
{
    use BonusTrait;

    #[ORM\Column(type: 'integer')]
    #[Assert\Positive()]
    private int $min;

    public function getMin(): int
    {
        return $this->min;
    }

    public function setMin(int $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getInfos(): array
    {
        return [
            BonusInfoLabelConstants::MINIMUM_LABEL => $this->getMin(),
        ];
    }
}
