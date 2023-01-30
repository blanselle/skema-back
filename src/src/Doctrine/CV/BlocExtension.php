<?php

namespace App\Doctrine\CV;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Bloc\Bloc;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

class BlocExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private RequestStack $requestStack) {}

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        //empty
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Bloc::class !== $resourceClass) {
            return;
        }

        $programChannelsId = $this->requestStack->getMainRequest()->get('programChannels', []);

        if (count($programChannelsId) > 0) {
            $rootAlias = $queryBuilder->getRootAliases()[0];
            $queryBuilder
                ->join(sprintf('%s.programChannels', $rootAlias), 'programChannels');
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->in('programChannels.id', ':_program_channels_id'),
                )
                ->setParameter('_program_channels_id', $programChannelsId);
        }
    }
}