<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\DatatableConstants;
use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Country|null find($id, $lockMode = null, $lockVersion = null)
 * @method Country|null findOneBy(array $criteria, array $orderBy = null)
 * @method Country[]    findAll()
 * @method Country[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
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
        if (!empty($filters['code'])) {
            $query->andWhere('LOWER(a.idCountry) like :code')
                ->setParameter('code', sprintf('%s', '%'.strtolower($filters['code']).'%'));
        }

        if (isset($filters['name']) && null != $filters['name']) {
            $query
                ->andWhere('upper(UNACCENT(a.name)) like upper(UNACCENT(:name))')
                ->setParameter('name', '%'.$filters['name'].'%')
            ;
        }
        if (!empty($filters['codeSISE'])) {
            $query->andWhere('LOWER(a.codeSISE) like :codeSISE')
                ->setParameter('codeSISE', sprintf('%s', '%'.strtolower($filters['codeSISE']).'%'));
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
