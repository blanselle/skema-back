<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\SlotConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SlotConfiguration>
 *
 * @method SlotConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlotConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlotConfiguration[]    findAll()
 * @method SlotConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SlotConfiguration::class);
    }

    public function save(SlotConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SlotConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
