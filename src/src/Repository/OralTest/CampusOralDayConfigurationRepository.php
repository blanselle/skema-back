<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\CampusOralDayConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CampusOralDayConfiguration>
 *
 * @method CampusOralDayConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampusOralDayConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampusOralDayConfiguration[]    findAll()
 * @method CampusOralDayConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampusOralDayConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampusOralDayConfiguration::class);
    }

    public function save(CampusOralDayConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CampusOralDayConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
