<?php

namespace App\Repository\CV\Bac;

use App\Entity\CV\Bac\BacDistinction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BacDistinction>
 *
 * @method BacDistinction|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacDistinction|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacDistinction[]    findAll()
 * @method BacDistinction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacDistinctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacDistinction::class);
    }
}
