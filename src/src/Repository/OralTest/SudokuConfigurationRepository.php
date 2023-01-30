<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\SudokuConfiguration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SudokuConfiguration>
 *
 * @method SudokuConfiguration|null find($id, $lockMode = null, $lockVersion = null)
 * @method SudokuConfiguration|null findOneBy(array $criteria, array $orderBy = null)
 * @method SudokuConfiguration[]    findAll()
 * @method SudokuConfiguration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SudokuConfigurationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SudokuConfiguration::class);
    }

    public function save(SudokuConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SudokuConfiguration $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return SudokuConfiguration[]
     */
    public function getAvailableConfiguration(): array
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->where('c.campusConfigurations IS NOT EMPTY')
        ;

        return $qb->getQuery()->getResult();
    }
}
