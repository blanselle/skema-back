<?php

declare(strict_types=1);

namespace App\Repository\CV;

use App\Entity\CV\BacSup;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BacSup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BacSup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BacSup[]    findAll()
 * @method BacSup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BacSupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BacSup::class);
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('bs');
        $qb
            ->where($qb->expr()->eq('bs.id', ':_id'))
            ->setParameter('_id', $id)
        ;

        return $qb->getQuery()->getOneOrNullResult()?->getCv()?->getStudent();
    }

    public function getMainsBacSup(int $cvId): array
    {
        $qb = $this->createQueryBuilder('bs');
        $qb
            ->where($qb->expr()->eq('bs.cv', ':_cv_id'))
            ->andWhere($qb->expr()->isNull('bs.dualPathBacSup'))
            ->setParameter('_cv_id', $cvId)
            ->orderBy('bs.year', 'asc')
        ;

        return $qb->getQuery()->getResult();
    }

    public function getBacSupsWithProgramChannel(int $programChannel): array
    {
        $qb = $this->createQueryBuilder('b')
            ->join('b.diploma', 'd')
            ->where(':programChannel MEMBER OF d.programChannels')
            ->setParameters(array('programChannel' => $programChannel))
            ->orderBy('d.name', 'asc')
        ;

        return $qb->getQuery()->getResult();
    }
}
