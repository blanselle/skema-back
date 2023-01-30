<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\Jury;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Jury>
 *
 * @method Jury|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jury|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jury[]    findAll()
 * @method Jury[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JuryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jury::class);
    }

    public function add(Jury $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Jury $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
