<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\SportLevelBonus;
use App\Entity\Traits\BonusRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SportLevelBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method SportLevelBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method SportLevelBonus[]    findAll()
 * @method SportLevelBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportLevelBonusRepository extends ServiceEntityRepository
{

    use BonusRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SportLevelBonus::class);
    }
}
