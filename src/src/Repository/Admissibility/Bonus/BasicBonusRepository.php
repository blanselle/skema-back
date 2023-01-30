<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\BasicBonus;
use App\Entity\Traits\BonusRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BasicBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method BasicBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method BasicBonus[]    findAll()
 * @method BasicBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasicBonusRepository extends ServiceEntityRepository
{
    
    use BonusRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BasicBonus::class);
    }
}
