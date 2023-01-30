<?php

declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\StudentWorkflowHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<StudentWorkflowHistory>
 *
 * @method StudentWorkflowHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentWorkflowHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentWorkflowHistory[]    findAll()
 * @method StudentWorkflowHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentWorkflowHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentWorkflowHistory::class);
    }

    public function save(StudentWorkflowHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(StudentWorkflowHistory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
