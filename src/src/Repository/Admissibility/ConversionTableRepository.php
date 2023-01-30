<?php

declare(strict_types=1);

namespace App\Repository\Admissibility;

use App\Entity\Admissibility\ConversionTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConversionTable>
 *
 * @method ConversionTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConversionTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConversionTable[]    findAll()
 * @method ConversionTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversionTableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConversionTable::class);
    }

    public function hasConversionTable(int $examClassificationId): bool
    {
        $qb = $this->createQueryBuilder('conversion_table');
        $qb
            ->join('conversion_table.param', 'param')
            ->join('param.admissibility', 'admissibility')
            ->where($qb->expr()->eq('admissibility.examClassification', ':_exam_classification_id'))
            ->setParameters([
                '_exam_classification_id' => $examClassificationId,
            ])
        ;

        $result = $qb->getQuery()->getResult();

        return count($result) > 0;
    }
}
