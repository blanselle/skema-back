<?php

declare(strict_types=1);

namespace App\Repository\Document;

use App\Entity\Document\DocumentReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DocumentReference|null find($id, $lockMode = null, $lockVersion = null)
 * @method DocumentReference|null findOneBy(array $criteria, array $orderBy = null)
 * @method DocumentReference[]    findAll()
 * @method DocumentReference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DocumentReference::class);
    }
}
