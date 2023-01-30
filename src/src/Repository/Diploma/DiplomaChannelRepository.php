<?php

declare(strict_types=1);

namespace App\Repository\Diploma;

use App\Constants\DatatableConstants;
use App\Entity\Diploma\DiplomaChannel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method DiplomaChannel|null find($id, $lockMode = null, $lockVersion = null)
 * @method DiplomaChannel|null findOneBy(array $criteria, array $orderBy = null)
 * @method DiplomaChannel[]    findAll()
 * @method DiplomaChannel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiplomaChannelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiplomaChannel::class);
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

        if (isset($filters['name']) && null != $filters['name']) {
            $query
                ->andWhere('upper(UNACCENT(a.name)) like upper(UNACCENT(:name))')
                ->setParameter('name', '%'.$filters['name'].'%')
            ;
        }
        if (!empty($filters['idDiploma'])) {
            $query->leftJoin('a.diplomas', 'd');
            $query->andWhere('d.id = :idDiploma')
                ->setParameter('idDiploma', sprintf('%d', $filters['idDiploma']));
        }

        if (empty($orders)) {
            $orders = [
                'a.createdAt' => 'DESC',
            ];
        }
        foreach ($orders as $key => $order) {
            $query->orderBy($key, $order);
        }

        if (empty($offset)) {
            $offset = 0;
        }
        $query->setFirstResult($offset);
        $query->setMaxResults($limit);

        return $query;
    }

    public function getDiplomaChannelsByDiploma(int $diploma): array
    {
        $qb = $this->createQueryBuilder('d')
            ->where(':diploma MEMBER OF d.diplomas')
            ->setParameters(array('diploma' => $diploma))
            ->orderBy('d.name', 'asc')
        ;
        return $qb->getQuery()->getResult();
    }
}
