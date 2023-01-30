<?php

declare(strict_types=1);

namespace App\Repository\Admissibility;

use App\Entity\Admissibility\Param;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Param>
 *
 * @method Param|null find($id, $lockMode = null, $lockVersion = null)
 * @method Param|null findOneBy(array $criteria, array $orderBy = null)
 * @method Param[]    findAll()
 * @method Param[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Param::class);
    }
}
