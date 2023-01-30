<?php

namespace App\Doctrine\Payment;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Payment\Order;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Symfony\Component\Security\Core\Security;

class OrderExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security) {}

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operationName = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operationName = null, array $context = []): void
    {
        // empty
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Order::class !== $resourceClass) {
            return;
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $student = $user->getStudent();
        if (null === $student) {
            throw new Exception('Student not found');
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->join(sprintf('%s.student', $rootAlias), 'student');
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in('student.id', ':_student_id'),
            )
            ->setParameter('_student_id', [$student->getId()])
        ;
    }
}