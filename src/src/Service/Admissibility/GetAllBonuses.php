<?php

declare(strict_types=1);

namespace App\Service\Admissibility;

use App\Constants\Admissibility\Bonus\BonusListConstants;
use Doctrine\ORM\EntityManagerInterface;

class GetAllBonuses
{
    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Find array of BonusInterface
     */
    public function get(array $criteria, string $sort = 'b.value', string $direction = 'desc'): array
    {
        $bonuses = [];
        foreach(BonusListConstants::getConsts() as $class) {
            /** @phpstan-ignore-next-line */
            $bonuses = array_merge($bonuses, $this->em->getRepository($class)->fetchBonuses($criteria, [$sort => $direction]));
        }

        return $bonuses;
    }
}
