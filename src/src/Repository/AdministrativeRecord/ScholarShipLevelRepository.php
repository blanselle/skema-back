<?php

namespace App\Repository\AdministrativeRecord;

use App\Entity\AdministrativeRecord\ScholarShipLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ScholarShipLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScholarShipLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScholarShipLevel[]    findAll()
 * @method ScholarShipLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScholarShipLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScholarShipLevel::class);
    }
}