<?php

declare(strict_types=1);

namespace App\Repository\Admissibility\LandingPage;

use App\Entity\Admissibility\LandingPage\AdmissibilityPurgeVarnish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdmissibilityPurgeVarnish>
 *
 * @method AdmissibilityPurgeVarnish|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdmissibilityPurgeVarnish|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdmissibilityPurgeVarnish[]    findAll()
 * @method AdmissibilityPurgeVarnish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdmissibilityPurgeVarnishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdmissibilityPurgeVarnish::class);
    }
}