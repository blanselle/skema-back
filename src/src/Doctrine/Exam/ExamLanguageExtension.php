<?php

namespace App\Doctrine\Exam;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Exam\ExamLanguage;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;
use Exception;

class ExamLanguageExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security) {}

    /**
     * @throws Exception
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        // empty
    }

    /**
     * @throws Exception
     */
    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (ExamLanguage::class !== $resourceClass || !$this->security->isGranted('ROLE_CANDIDATE')) {
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
            ->join(sprintf('%s.programChannels', $rootAlias), 'program_channels');
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->in('program_channels.id', ':_program_channel'),
            )
            ->setParameter('_program_channel', [$student->getProgramChannel()->getId()])
        ;
    }
}