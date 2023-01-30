<?php

declare(strict_types=1);

namespace App\Repository\Diploma;

use App\Entity\Diploma\StudentDiploma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentDiploma|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentDiploma|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentDiploma[]    findAll()
 * @method StudentDiploma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentDiplomaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentDiploma::class);
    }
}
