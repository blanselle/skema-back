<?php

declare(strict_types=1);

namespace App\Entity\Admissibility\Bonus;

use App\Constants\Admissibility\Bonus\BonusInfoLabelConstants;
use App\Constants\CV\Experience\ExperienceTypeConstants;
use App\Entity\Traits\BonusTrait;
use App\Interface\BonusInterface;
use App\Repository\Admissibility\Bonus\ExperienceBonusRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExperienceBonusRepository::class)]
#[ORM\Table(name: 'admissibility_bonus_experience')]
class ExperienceBonus implements BonusInterface
{
    use BonusTrait;

    #[ORM\Column(type: 'string', length: 150, unique: false)]
    #[Assert\Choice(
        callback: [ExperienceTypeConstants::class, 'getConsts'],
        message: 'The type {{ value }} is not in {{ choices }}',
    )]
    private string $type;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Positive()]
    private int $duration;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * La durée est en mois
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getInfos(): array
    {
        return [
            BonusInfoLabelConstants::EXPERIENCE_TYPE_LABEL => $this->getType(),
            BonusInfoLabelConstants::DURATION_LABEL => $this->getDuration(),
        ];
    }
}
