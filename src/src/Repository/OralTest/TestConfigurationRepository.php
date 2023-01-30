<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\TestConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestConfiguration>
 *
 * @method TestConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestConfiguration[]    findAll()
 * @method TestConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestConfiguration::class);
    }

    public function save(TestConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TestConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
