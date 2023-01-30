<?php

declare(strict_types=1);

namespace App\Repository\Notification;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use App\Constants\DatatableConstants;
use App\Constants\User\UserRoleConstants;
use App\Entity\Notification\Notification;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function filterQueryBuilder(
        QueryBuilder $query,
        array $params,
        int $limit = DatatableConstants::TABLE_PAGINATION_LENGTH,
        int $offset = DatatableConstants::TABLE_PAGINATION_START,
        ?array $orders = []
    ): QueryBuilder {
        $query
            ->leftJoin('a.sender', 'u')
            ->leftJoin('a.receiver', 'user_sender')
            ->leftJoin('u.student', 'sender_student')
            ->leftJoin('sender_student.programChannel', 'sender_pc')
        ;
        
        $filters = $params['filters'];
        if (isset($filters['student']) && null != $filters['student']) {
            $query->andWhere('a.sender = :sender')
                ->setParameter('sender', $params['student']->getUser());

            $query->orWhere('a.receiver = :receiver')
                ->setParameter('receiver', $params['student']->getUser());
            $query->andWhere('a.parent is null');
        } elseif (!empty($params['sender'])) { 
            $query->andWhere('a.sender = :sender')
                ->setParameter('sender', $params['sender']);

            $query
                ->orWhere("a.roleSender LIKE :ROLE_ADMIN")
                ->orWhere("a.roleSender LIKE :ROLE_RESPONSABLE")
                ->orWhere("a.roleSender LIKE :ROLE_COORDINATOR")
                ->orWhere("a.sender is null")
                ->setParameter('ROLE_ADMIN', '%'.UserRoleConstants::ROLE_ADMIN.'%')
                ->setParameter('ROLE_RESPONSABLE', '%'.UserRoleConstants::ROLE_RESPONSABLE.'%')
                ->setParameter('ROLE_COORDINATOR', '%'.UserRoleConstants::ROLE_COORDINATOR.'%');
        } elseif (!empty($params['user']) ) {
            $query->andWhere('a.receiver = :receiver')
                ->setParameter('receiver', $params['user']);

            $query
                ->orWhere("a.roles LIKE :role")
                ->setParameter('role', '%'.$params['user']->getRoles()[0].'%');
        }

        if (empty($params['private'])) {
            $query->andWhere('a.private = :private')
                  ->setParameter('private', 'false');
        }

        if (null !== ($filters['identifier']?? null)) {
            $query->andWhere($query->expr()->like('LOWER(a.identifier)', ':identifier'))
                ->setParameter('identifier', sprintf('%s', '%'.strtolower($filters['identifier']).'%'))
            ;
        }
        if (null !== ($filters['firstname']?? null) && !empty($params['sender'])) {
            $query->andWhere($query->expr()->like('LOWER(UNACCENT(user_sender.firstName))', 'UNACCENT(:firstname)'))
                ->setParameter('firstname', sprintf('%s', '%'.strtolower($filters['firstname']).'%'))
            ;
        }
        if (null !== ($filters['lastname']?? null) && !empty($params['lastname'])) {
            $query->andWhere($query->expr()->like('LOWER(UNACCENT(user_sender.lastName))', 'UNACCENT(:lastname)'))
                ->setParameter('lastname', sprintf('%s', '%'.strtolower($filters['lastname']).'%'))
            ;
        }
        if (null !== ($filters['firstname']?? null) && empty($params['sender'])) {
            $query->andWhere($query->expr()->like('LOWER(UNACCENT(u.firstName))', 'UNACCENT(:firstname)'))
                ->setParameter('firstname', sprintf('%s', '%'.strtolower($filters['firstname']).'%'))
            ;
        }
        if (null !== ($filters['lastname']?? null) && empty($params['sender'])) {
            $query->andWhere($query->expr()->like('LOWER(UNACCENT(u.lastName))', 'UNACCENT(:lastname)'))
                ->setParameter('lastname', sprintf('%s', '%'.strtolower($filters['lastname']).'%'))
            ;
        }
        if (null !== ($filters['subject']?? null)) {
            $query->andWhere($query->expr()->like('LOWER(a.subject)', ':subject'))
                ->setParameter('subject', sprintf('%s', '%'.strtolower($filters['subject']).'%'))
            ;
        }
        if (null !== ($filters['comment']?? null)) {
            $query->andWhere($query->expr()->like('LOWER(a.comment)', ':comment'))
                ->setParameter('comment', sprintf('%s', '%'.strtolower($filters['comment']).'%'))
            ;
        }

        if (null !== ($filters['read']?? null)) {
            $query->andWhere("a.read = :read")
                ->setParameter('read', $filters['read'])
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

    public function countResult(QueryBuilder $query): int
    {
        $query = clone $query;
        $query->resetDQLPart('select');
        $query->resetDQLPart('orderBy');
        $query->setMaxResults(1);
        $query->setFirstResult(0);
        $query->select('count(a.id)');
        return $query->getQuery()->getSingleScalarResult();
    }

    public function createNotificationQueryBuilder(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('n');
        return $qb
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('n.sender', ':user'),
                    $qb->expr()->eq('n.receiver', ':user'),
                )
            )
            ->setParameter('user', $user)
        ;
    }
}
