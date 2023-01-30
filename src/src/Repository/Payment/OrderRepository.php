<?php

namespace App\Repository\Payment;

use App\Entity\Payment\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function save(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getPaginatorQuery(array $criteria = []): Query
    {
        $qb = $this->createQueryBuilder('o');

        $qb
            ->join('o.student', 'student')
            ->leftJoin('o.examSession', 'exam_session')
            ->leftJoin('exam_session.examClassification', 'exam_classification')
            ->join('o.payments', 'payment')
        ;

        foreach ($criteria as $key => $criterion) {
            $placeholder = sprintf('_%s', str_replace('.', '_', $key));
            $qb
                ->andWhere($qb->expr()->eq($key, ":{$placeholder}"))
                ->setParameter("{$placeholder}", $criterion)
            ;
        }

        return $qb->getQuery();
    }
}
