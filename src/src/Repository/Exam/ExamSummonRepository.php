<?php

namespace App\Repository\Exam;

use App\Entity\Exam\ExamSummon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ExamSummon>
 *
 * @method ExamSummon|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamSummon|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamSummon[]    findAll()
 * @method ExamSummon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamSummonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamSummon::class);
    }
}
