<?php

declare(strict_types=1);

namespace App\Repository\Diploma;

use App\Constants\DatatableConstants;
use App\Entity\Diploma\Diploma;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Diploma|null find($id, $lockMode = null, $lockVersion = null)
 * @method Diploma|null findOneBy(array $criteria, array $orderBy = null)
 * @method Diploma[]    findAll()
 * @method Diploma[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiplomaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Diploma::class);
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
        if (!empty($filters['idDiplomaChannel'])) {
            $query->leftJoin('a.diplomaChannels', 'dc');
            $query->andWhere('dc.id = :idDiplomaChannel')
                ->setParameter('idDiplomaChannel', sprintf('%d', $filters['idDiplomaChannel']));
        }

        if (isset($filters['name']) && null != $filters['name']) {
            $query
                ->andWhere('upper(UNACCENT(a.name)) like upper(UNACCENT(:name))')
                ->setParameter('name', '%'.$filters['name'].'%')
            ;
        }

        if (!empty($filters['programChannel'])) {
            $query->join('a.programChannels', 'p');
            $query->andWhere('p.id = :programChannel')
                ->setParameter('programChannel', sprintf('%d', $filters['programChannel']));
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

    public function getDiplomasByProgramChannel(int $programChannel): array
    {
        $qb = $this->createQueryBuilder('d')
            ->where(':programChannel MEMBER OF d.programChannels')
            ->setParameters(array('programChannel' => $programChannel))
            ->orderBy('d.name', 'asc')
        ;
        return $qb->getQuery()->getResult();
    }
}
