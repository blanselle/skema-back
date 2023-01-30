<?php

declare(strict_types=1);

namespace App\Repository\CV;

use App\Constants\DatatableConstants;
use App\Entity\CV\Language;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Language|null find($id, $lockMode = null, $lockVersion = null)
 * @method Language|null findOneBy(array $criteria, array $orderBy = null)
 * @method Language[]    findAll()
 * @method Language[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanguageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Language::class);
    }


    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params = [],
        int $limit = DatatableConstants::TABLE_PAGINATION_LENGTH,
        int $offset = DatatableConstants::TABLE_PAGINATION_START,
        ?array $orders = []
    ): QueryBuilder {
        $query->select('a');

        $filters = $params['filters'];

        if (isset($filters['label']) && null != $filters['label']) {
            $query
                ->andWhere('upper(UNACCENT(a.label)) like upper(UNACCENT(:label))')
                ->setParameter('label', '%'.$filters['label'].'%')
            ;
        }
        if (isset($filters['code']) && null != $filters['code']) {
            $query
                ->andWhere('upper(UNACCENT(a.code)) like upper(UNACCENT(:code))')
                ->setParameter('code', '%'.$filters['code'].'%')
            ;
        }

        if (empty($orders)) {
            $orders = [
                'a.label' => 'asc',
            ];
        }
        foreach ($orders as $key => $order) {
            $query->orderBy($key, $order);
        }

        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query;
    }
}
