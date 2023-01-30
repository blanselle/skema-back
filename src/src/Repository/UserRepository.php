<?php

declare(strict_types=1);

namespace App\Repository;

use App\Constants\User\UserRoleConstants;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App\Entity\User u
                WHERE u.email = :query'
        )
            ->setParameter('query', $identifier)
            ->getOneOrNullResult()
        ;
    }

    public function findAllExceptCandidate(): array
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->andWhere($qb->expr()->notLike('u.roles', ':role'))
            ->setParameter('role', '%' . UserRoleConstants::ROLE_CANDIDATE . '%')
            ->orderBy('u.createdAt', 'desc')
            ->getQuery()->getResult()
        ;
    }

    public function findUsersByRole(string $role): array
    {
        $qb = $this->createQueryBuilder('u');
        return $qb
            ->andWhere($qb->expr()->like('u.roles', ':role'))
            ->setParameter('role', '%' . sprintf('%s', $role) . '%')
            ->orderBy('u.lastName', 'asc')
            ->getQuery()->getResult()
        ;
    }

    public function emailExist(string $email): bool
    {
        $qb = $this->createQueryBuilder('u');

        $result = $qb
            ->select('COUNT(u.id) as count')
            /** @phpstan-ignore-next-line */
            ->andWhere($qb->expr()->like($qb->expr()->lower('u.email'), $qb->expr()->lower(':email')))
            ->setParameter('email', $email)
            ->getQuery()
            ->getSingleResult()
        ;

        return $result['count'] > 0;
    }

    public function findStudent(mixed $id): ?Student
    {
        $qb = $this->createQueryBuilder('u');

        $qb
            ->andWhere($qb->expr()->eq('u.id', ':_id'))
            ->setParameter('_id', $id);

        $user = $qb->getQuery()->getOneOrNullResult();

        return $user?->getStudent();
    }
}
