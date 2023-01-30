<?php

declare(strict_types=1);

namespace App\Repository\Faq;

use App\Constants\DatatableConstants;
use App\Entity\Faq\Faq;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Faq|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faq|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faq[]    findAll()
 * @method Faq[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaqRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private FaqTopicRepository $faqTopicRepository
    ) {
        parent::__construct($registry, Faq::class);
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params = [],
        int $limit = DatatableConstants::TABLE_PAGINATION_LENGTH,
        int $offset = DatatableConstants::TABLE_PAGINATION_START,
        ?array $orders = []
    ): QueryBuilder {
        $filters = $params['filters'];
        if (isset($filters['question']) && null != $filters['question']) {
            $query
                ->andWhere('upper(UNACCENT(a.question)) like upper(UNACCENT(:question))')
                ->setParameter('question', '%'.$filters['question'].'%')
            ;
        }
        if (!empty($filters['topic'])) {
            $topic = $this->faqTopicRepository->findOneById($filters['topic']);

            $query->andWhere($query->expr()->isMemberOf(':topic', 'a.topics'))
                ->setParameter('topic', $topic)
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
