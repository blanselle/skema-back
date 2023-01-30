<?php

declare(strict_types=1);

namespace App\Repository\CV;

use App\Entity\CV\SchoolReport;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SchoolReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method SchoolReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method SchoolReport[]    findAll()
 * @method SchoolReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SchoolReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SchoolReport::class);
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('sr');
        $qb
            ->andWhere($qb->expr()->eq('sr.id', ':_id'))
            ->setParameter('_id', $id)
        ;

        return $qb->getQuery()->getOneOrNullResult()?->getBacSup()?->getCv()?->getStudent();
    }

    public function findByStudent(Student $student): array
    {
        $qb = $this->createQueryBuilder('sr');
        $qb
            ->join('sr.bacSup', 'bs')
            ->join('bs.cv', 'cv')
            ->where($qb->expr()->eq('cv.student', ':_student'))
            ->setParameter('_student', $student)
        ;

        return $qb->getQuery()->getResult();
    }
}
