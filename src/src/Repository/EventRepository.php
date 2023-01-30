<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\DatatableConstants;
use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
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

        if (empty($orders)) {
            $orders = [
                'a.createdAt' => 'DESC',
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
