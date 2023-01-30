<?php

declare(strict_types=1);

namespace App\Filter\ExamClassification;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use App\Constants\Exam\ExamSessionTypeConstants;
use App\Entity\Exam\ExamSession;
use App\Entity\User;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\PropertyInfo\Type;

class ActiveExamSessionFilter extends AbstractContextAwareFilter
{
    public function __construct(private Security $security, ManagerRegistry $managerRegistry, ?RequestStack $requestStack = null, LoggerInterface $logger = null, array $properties = null, NameConverterInterface $nameConverter = null)
    {
        parent::__construct($managerRegistry, $requestStack, $logger, $properties, $nameConverter);
    }

    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ('active' !== $property) {
            return;
        }
        
        /** @var QueryBuilder */
        $subQueryQueryBuilder = $this->getManagerRegistry()->getRepository(ExamSession::class)->createQueryBuilder('es')
            ->leftJoin('es.examStudents', 'est')
            ->groupBy('es.id')
            ->having($queryBuilder->expr()->gt('es.numberOfPlaces', $queryBuilder->expr()->count('es.id')))
        ;

        /** @var User $currentUser */
        $currentUser = $this->security->getUser();

        $queryBuilder
           ->join('o.examSessions', 'ess')
           ->join('o.programChannels', 'pc')
           ->where($queryBuilder->expr()->in('ess', $subQueryQueryBuilder->getDQL()))
           ->andWhere($queryBuilder->expr()->eq('ess.type', ':type'))
           ->andWhere($queryBuilder->expr()->lt(':date', 'ess.dateStart'))
           ->andWhere($queryBuilder->expr()->in(':programChannel', 'pc.id'))
           ->setParameter('type', ExamSessionTypeConstants::TYPE_INSIDE)
           ->setParameter('date', (new DateTime()))
           ->setParameter('programChannel', $currentUser->getStudent()->getProgramChannel()->getId())
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [
            'active' => [
                'property' => null,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Search classification skema type that hasn\'t started yet from the same programChannel for the logged in user with still places',
                    'name' => 'Active',
                    'type' => null,
                ],
            ]
        ];

        return $description;
    }
}
