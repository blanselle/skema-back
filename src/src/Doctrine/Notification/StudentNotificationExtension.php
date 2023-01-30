<?php

// api/src/Doctrine/CurrentUserExtension.php

namespace App\Doctrine\Notification;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Notification\Notification;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

final class StudentNotificationExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Notification::class !== $resourceClass || !$this->security->isGranted('ROLE_CANDIDATE')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq(sprintf('%s.sender', $rootAlias), ':user'),
                    $queryBuilder->expr()->eq(sprintf('%s.receiver', $rootAlias), ':user'),
                )
            )
            ->andWhere($queryBuilder->expr()->eq(sprintf('%s.private', $rootAlias), ':_private'))
            ->setParameter('user', $this->security->getUser())
            ->setParameter('_private', false)
        ;
    }
}
