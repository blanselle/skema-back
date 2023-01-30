<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\OralTestStudent;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OralTestStudent>
 *
 * @method OralTestStudent|null find($id, $lockMode = null, $lockVersion = null)
 * @method OralTestStudent|null findOneBy(array $criteria, array $orderBy = null)
 * @method OralTestStudent[]    findAll()
 * @method OralTestStudent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OralTestStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OralTestStudent::class);
    }

    public function findByStudentByState(
        Student $student,
        array $states
    ): array {
        $qb = $this->createQueryBuilder('ots');

        return $qb
            ->where($qb->expr()->eq('ots.student', ':student'))
            ->andWhere($qb->expr()->in('ots.state', ':states'))
            ->setParameter('student', $student)
            ->setParameter('states', $states)
            ->getQuery()->getResult();
    }
}
