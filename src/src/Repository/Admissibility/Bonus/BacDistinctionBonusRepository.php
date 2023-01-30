<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\BacDistinctionBonus;
use App\Entity\Traits\BonusRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BacDistinctionBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacDistinctionBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacDistinctionBonus[]    findAll()
 * @method BacDistinctionBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacDistinctionBonusRepository extends ServiceEntityRepository
{

    use BonusRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacDistinctionBonus::class);
    }
}
