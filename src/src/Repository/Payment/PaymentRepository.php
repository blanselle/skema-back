<?php

namespace App\Repository\Payment;

use App\Constants\Payment\PaymentWorkflowStateConstants;
use App\Entity\Payment\Payment;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->andWhere($qb->expr()->eq('p.id', ':_id'))
            ->setParameter('_id', $id)
        ;

        /** @var Payment|null $payment */
        $payment = $qb->getQuery()->getOneOrNullResult();

        return $payment?->getIndent()?->getStudent();
    }

    public function fetchCreatedPayment(Student $student): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->join('p.indent', 'o')
            ->join('o.student', 's')
            ->where($qb->expr()->eq('s.id', ':_id'))
            ->setParameter('_id', $student->getId())
            ->andWhere($qb->expr()->eq('p.state', ':_state'))
            ->setParameter('_state', PaymentWorkflowStateConstants::STATE_CREATED)
        ;

        return $qb->getQuery()->getResult();
    }
}
