<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\PlanningInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlanningInfo>
 *
 * @method PlanningInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlanningInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlanningInfo[]    findAll()
 * @method PlanningInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanningInfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlanningInfo::class);
    }

    public function add(PlanningInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PlanningInfo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
