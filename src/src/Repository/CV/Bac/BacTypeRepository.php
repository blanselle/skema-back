<?php

declare(strict_types=1);

namespace App\Repository\CV\Bac;

use App\Entity\CV\Bac\BacChannel;
use App\Entity\CV\Bac\BacType;
use App\Service\Cv\GetTypeBacFromYear;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BacType|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacType|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacType[]    findAll()
 * @method BacType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacType::class);
    }

    public function getBacTypesByBacChannel(?BacChannel $bacChannel, ?int $year = null): array
    {

        $qb = $this->createQueryBuilder('bt');
        if(null !== $bacChannel) {
            $qb->where($qb->expr()->eq('bt.bacChannel', ':bacChannel'))
                ->setParameters(['bacChannel' => $bacChannel])
            ;
        }

        if(null !== $year) {
            $tag = (new GetTypeBacFromYear())->get($year);
            $qb
                ->andWhere('bt.tags LIKE :tag')
                ->setParameter('tag', "%\"$tag\"%")
            ;
        }

        $qb->orderBy('bt.name', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
