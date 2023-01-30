<?php

declare(strict_types=1);

namespace App\Repository\Parameter;

use App\Entity\Parameter\ParameterKey;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ParameterKey|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParameterKey|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParameterKey[]    findAll()
 * @method ParameterKey[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterKeyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ParameterKey::class);
    }
}
