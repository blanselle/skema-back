<?php

declare(strict_types=1);

namespace App\Repository\Exam;

use App\Entity\Exam\ExamRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ExamRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExamRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExamRoom[]    findAll()
 * @method ExamRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExamRoom::class);
    }
}
