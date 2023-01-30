<?php

namespace App\Repository\Exam;

use App\Entity\Exam\ExamClassification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExamClassification|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamClassification|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamClassification[]    findAll()
 * @method ExamClassification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamClassificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamClassification::class);
    }
}