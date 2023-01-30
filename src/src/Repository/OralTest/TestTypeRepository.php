<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\TestType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TestType>
 *
 * @method TestType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestType[]    findAll()
 * @method TestType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestType::class);
    }

    public function save(TestType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TestType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
