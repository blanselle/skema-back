<?php

declare(strict_types=1);

namespace App\Repository\CV\Bac;

use App\Entity\CV\Bac\BacOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BacOption|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacOption|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacOption[]    findAll()
 * @method BacOption[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacOption::class);
    }

    public function getBacOptionsByBacType(array|null $bacTypes): array
    {
        $qb = $this->createQueryBuilder('o');

        if($bacTypes !== null) {
            $qb
                ->leftJoin('o.bacTypes', 'b')
                ->andWhere($qb->expr()->in('b.id', ':bacTypes'))
                ->orderBy('o.name', 'asc')
                ->setParameter('bacTypes', $bacTypes)
            ;
        }

        $qb->orderBy('o.name', 'ASC');

        return $qb->getQuery()->getResult()?? [];
    }
}
