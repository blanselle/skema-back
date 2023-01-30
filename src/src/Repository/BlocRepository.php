<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\DatatableConstants;
use App\Entity\Bloc\Bloc;
use App\Entity\ProgramChannel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Bloc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bloc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bloc[]    findAll()
 * @method Bloc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bloc::class);
    }

    public function add(Bloc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Bloc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findActiveByKey(string $key): ?Bloc
    {
        $qb = $this->createQueryBuilder('b');
        return $qb
            ->andWhere($qb->expr()->like('b.key', ':key'))
            ->andWhere($qb->expr()->eq('b.active', ':active'))
            ->setParameter('key', $key)
            ->setParameter('active', true)
            ->orderBy('b.position', 'ASC')
            ->getQuery()->getOneOrNullResult()
        ;
    }

    public function findActiveByKeyAndProgramChannel(string $key, ProgramChannel $programChannel): ?Bloc
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->andWhere($qb->expr()->like('b.key', ':key'))
            ->andWhere($qb->expr()->eq('b.active', ':active'))

            ->leftJoin('b.programChannels', 'pc')
            ->andWhere($qb->expr()->eq('pc.id', ':program_channel'))
            ->setParameter('program_channel', $programChannel->getId())
            ->setParameter('key', $key)
            ->setParameter('active', true)
            ->orderBy('b.position', 'ASC')
            ->setMaxResults(1)
        ;
        
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params = [],
        int $limit = DatatableConstants::TABLE_PAGINATION_LENGTH,
        int $offset = DatatableConstants::TABLE_PAGINATION_START,
        ?array $orders = []
    ): QueryBuilder {
        $query->select('a');
        $query->innerJoin('a.tag', 't');

        $filters = $params['filters'];

        if (isset($filters['label']) && null != $filters['label']) {
            $query
                ->andWhere('upper(UNACCENT(a.label)) like upper(UNACCENT(:label))')
                ->setParameter('label', '%'.$filters['label'].'%')
            ;
        }
        if (!empty($filters['tag'])) {
            $query->andWhere('LOWER(t.label) like :tag')
                ->setParameter('tag', sprintf('%s', '%'.strtolower($filters['tag']).'%'));
        }
        if (!empty($filters['key'])) {
            $query->andWhere('LOWER(a.key) like :key')
                ->setParameter('key', sprintf('%s', '%'.strtolower($filters['key']).'%'));
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
