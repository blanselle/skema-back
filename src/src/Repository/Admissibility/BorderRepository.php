<?php

declare(strict_types=1);

namespace App\Repository\Admissibility;

use App\Entity\Admissibility\Border;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Border>
 *
 * @method Border|null find($id, $lockMode = null, $lockVersion = null)
 * @method Border|null findOneBy(array $criteria, array $orderBy = null)
 * @method Border[]    findAll()
 * @method Border[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BorderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Border::class);
    }
}
