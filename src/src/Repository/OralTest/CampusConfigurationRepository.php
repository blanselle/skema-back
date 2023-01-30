<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\CampusConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampusConfiguration>
 *
 * @method CampusConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampusConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampusConfiguration[]    findAll()
 * @method CampusConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampusConfiguration::class);
    }

    public function save(CampusConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CampusConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
