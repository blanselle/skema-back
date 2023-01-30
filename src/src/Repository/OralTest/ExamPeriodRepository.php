<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\ExamPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExamPeriod>
 *
 * @method ExamPeriod|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamPeriod|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamPeriod[]    findAll()
 * @method ExamPeriod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamPeriod::class);
    }

    public function add(ExamPeriod $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ExamPeriod $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
