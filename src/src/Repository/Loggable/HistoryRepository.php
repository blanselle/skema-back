<?php

namespace App\Repository\Loggable;

use App\Entity\Loggable\History;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<History>
 *
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    public function add(History $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(History $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getHistories(Student $student, array $orderBy = null): array
    {
        $qb = $this->createQueryBuilder('h');
        $qb
            ->where($qb->expr()->eq('h.student', ':_student'))
            ->setParameter('_student', $student)
        ;

        if (!empty($orderBy)) {
            foreach($orderBy as $sort => $direction) {
                $qb->addOrderBy($sort, $direction);
            }
        }

        $result = $qb->getQuery()->getResult();

        // Need to filter on data to remove empty datum
        return array_filter($result, function($history) {
            $data = array_filter($history->getData()?? [], function ($datum, $k) {
                if (!is_null($datum)) {
                    return [$k => $datum];
                }
            }, ARRAY_FILTER_USE_BOTH);

            if (count($data) > 0) {
                $history->setData($data);
                return $history;
            }
        });
    }
}
