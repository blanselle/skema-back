<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\Bonus;

use App\Entity\Admissibility\Bonus\BacTypeBonus;
use App\Entity\Traits\BonusRepositoryTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BacTypeBonus|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacTypeBonus|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacTypeBonus[]    findAll()
 * @method BacTypeBonus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacTypeBonusRepository extends ServiceEntityRepository
{

    use BonusRepositoryTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacTypeBonus::class);
    }
}
