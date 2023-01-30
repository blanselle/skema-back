<?php

namespace App\Repository\OralTest;

use App\Entity\OralTest\SlotType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SlotType>
 *
 * @method SlotType|null find($id, $lockMode = null, $lockVersion = null)
 * @method SlotType|null findOneBy(array $criteria, array $orderBy = null)
 * @method SlotType[]    findAll()
 * @method SlotType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlotTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SlotType::class);
    }

    public function save(SlotType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SlotType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
