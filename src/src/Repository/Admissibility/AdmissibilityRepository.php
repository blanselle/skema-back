<?php

declare(strict_types=1);

namespace App\Repository\Admissibility;

use App\Entity\Admissibility\Admissibility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Admissibility>
 *
 * @method Admissibility|null find($id, $lockMode = null, $lockVersion = null)
 * @method Admissibility|null findOneBy(array $criteria, array $orderBy = null)
 * @method Admissibility[]    findAll()
 * @method Admissibility[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdmissibilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admissibility::class);
    }
}
