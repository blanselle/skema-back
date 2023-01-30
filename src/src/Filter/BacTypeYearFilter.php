<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Service\Cv\GetTypeBacFromYear;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class BacTypeYearFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if ('year' !== $property) {
            return;
        }

        $tag = (new GetTypeBacFromYear())->get(intval($value));

        $queryBuilder
            ->andWhere('o.tags LIKE :tag')
            ->setParameter(':tag', "%\"$tag\"%");
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [
            'year' => [
                'property' => 'year',
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'swagger' => [
                    'description' => 'Filter the bac type by year of graduation',
                    'name' => 'Year filter',
                    'type' => 'year',
                ],
            ]
        ];

        return $description;
    }
}
