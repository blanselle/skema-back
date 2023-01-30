<?php

namespace App\Repository\AdministrativeRecord;

use App\Entity\AdministrativeRecord\SportLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SportLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method SportLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method SportLevel[]    findAll()
 * @method SportLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SportLevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SportLevel::class);
    }
}