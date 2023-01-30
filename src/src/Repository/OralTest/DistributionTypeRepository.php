<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\DistributionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DistributionType>
 *
 * @method DistributionType|null find($id, $lockMode = null, $lockVersion = null)
 * @method DistributionType|null findOneBy(array $criteria, array $orderBy = null)
 * @method DistributionType[]    findAll()
 * @method DistributionType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DistributionTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistributionType::class);
    }

    public function save(DistributionType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(DistributionType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
