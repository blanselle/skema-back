<?php

declare(strict_types=1);

namespace App\Repository\CV\Bac;

use App\Entity\CV\Bac\Bac;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Bac|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bac|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bac[]    findAll()
 * @method Bac[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bac::class);
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->where($qb->expr()->eq('b.id', ':_id'))
            ->setParameter('_id', $id)
        ;

        return $qb->getQuery()->getOneOrNullResult()?->getCv()?->getStudent();
    }
}
