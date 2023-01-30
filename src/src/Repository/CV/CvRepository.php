<?php

declare(strict_types=1);

namespace App\Repository\CV;

use App\Entity\CV\Cv;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cv|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cv|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cv[]    findAll()
 * @method Cv[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CvRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cv::class);
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('cv');
        $qb
            ->andWhere($qb->expr()->eq('cv.id', ':_id'))
            ->setParameter('_id', $id)
        ;

        $cv = $qb->getQuery()->getOneOrNullResult();

        return $cv?->getStudent();
    }
}
