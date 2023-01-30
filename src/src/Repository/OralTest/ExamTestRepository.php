<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\ExamTest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExamTest>
 *
 * @method ExamTest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamTest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamTest[]    findAll()
 * @method ExamTest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamTestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamTest::class);
    }

    public function add(ExamTest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExamTest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
