<?php

declare(strict_types=1);

namespace App\Repository\Parameter;

use App\Constants\DatatableConstants;
use App\Entity\Campus;
use App\Entity\Parameter\Parameter;
use App\Entity\Parameter\ParameterKey;
use App\Entity\ProgramChannel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parameter|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameter|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameter[]    findAll()
 * @method Parameter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParameterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameter::class);
    }

    public function add(Parameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Parameter $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params = [],
        int $limit = DatatableConstants::TABLE_PAGINATION_LENGTH,
        int $offset = DatatableConstants::TABLE_PAGINATION_START,
        ?array $orders = []
    ): QueryBuilder {
        $query->select('a');
        $query->join('a.key', 'k');

        $filters = $params['filters'];
        if (!empty($filters['name'])) {
            $query->andWhere('LOWER(k.name) like :name')
                ->setParameter('name', sprintf('%s', '%'.strtolower($filters['name']).'%'));
        }
        if (isset($filters['descr']) && null != $filters['descr']) {
            $query
                ->andWhere('upper(UNACCENT(k.description)) like upper(UNACCENT(:descr))')
                ->setParameter('descr', '%' . $filters['descr'] . '%');
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

    public function findOneParameterWithKeyAndProgramChannel(ParameterKey $key, ?ProgramChannel $programChannel): ?Parameter
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.key = :key')
            ->setParameter('key', $key)
            ->setMaxResults(1)
        ;

        if (null !== $programChannel) {
            $qb
                ->andWhere(':programChannel MEMBER OF p.programChannels')
                ->setParameter('programChannel', $programChannel)
            ;
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneParameterWithKeyAndCampusAndProgramChannel(ParameterKey $key, Campus $campus, ProgramChannel $programChannel): ?Parameter
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->andWhere($qb->expr()->eq('p.key', ':key'))
            ->setParameter('key', $key)
            ->andWhere($qb->expr()->isMemberOf(':programChannel', 'p.programChannels'))
            ->setParameter('programChannel', $programChannel)
            ->andWhere($qb->expr()->isMemberOf(':campus', 'p.campuses'))
            ->setParameter('campus', $campus)
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findOneParameterByKeyName(string $key): ?Parameter
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->leftJoin('p.key', 'k')
            ->andWhere($qb->expr()->eq('k.name', ':key'))
            ->setParameter('key', $key)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneParameterByKeyNameAndProgramChannel(string $key, ProgramChannel $programChannel): ?Parameter
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->leftJoin('p.key', 'k')
            ->andWhere($qb->expr()->eq('k.name', ':key'))
            ->setParameter('key', $key)
            ->andWhere(':programChannel MEMBER OF p.programChannels')
            ->setParameter('programChannel', $programChannel)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findParameterByKeyName(string $key): array
    {
        $qb = $this->createQueryBuilder('p');

        return $qb
            ->leftJoin('p.key', 'k')
            ->andWhere($qb->expr()->eq('k.name', ':key'))
            ->setParameter('key', $key)
            ->getQuery()
            ->getResult()
            ;
    }
}
