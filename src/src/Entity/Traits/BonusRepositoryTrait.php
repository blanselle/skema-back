<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\ProgramChannel;
use App\Interface\BonusInterface;
use Doctrine\ORM\QueryBuilder;

trait BonusRepositoryTrait
{
    public function findOneByCategory(string $categoryName, ProgramChannel $programChannel): ?BonusInterface
    {
        /** @var BonusInterface|null $bonus */
        $bonus = $this->getQueryBuilderCategory($categoryName, $programChannel)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        return $bonus;
    }

    public function findByCategory(string $categoryName, ProgramChannel $programChannel): array
    {
        return $this->getQueryBuilderCategory($categoryName, $programChannel)
            ->orderBy('b.value', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function fetchBonuses(array $criteria, array $orderBy = null): array
    {
        $qb = $this->createQueryBuilder('b');
        $qb
            ->leftJoin('b.category', 'c')
            ->leftJoin('b.programChannel', 'p')
        ;

        foreach ($criteria as $key => $criterion) {
            $qb
                ->andWhere($qb->expr()->eq("b.{$key}", ":{$key}"))
                ->setParameter($key, $criterion)
            ;
        }

        foreach ($orderBy as $sort => $direction) {
            $qb->addOrderBy($sort, $direction);
        }

        return $qb->getQuery()->getResult();
    }

    public function getQueryBuilderCategory(string $categoryName, ProgramChannel $programChannel): QueryBuilder
    {
        $qb = $this->createQueryBuilder('b');
        return $qb
            ->leftJoin('b.category', 'c')
            ->leftJoin('b.programChannel', 'p')
            ->andWhere($qb->expr()->eq('c.key', ':category'))
            ->andWhere($qb->expr()->eq('p', ':programChannel'))
            ->setParameter('category', $categoryName)
            ->setParameter('programChannel', $programChannel)
        ;
    }
}
