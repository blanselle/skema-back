<?php


declare(strict_types=1);

namespace App\Event\Datatable;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class DatatableCreateQueryEvent extends Event
{
    public const NAME = 'datatable.query.created';

    public function __construct(
        protected Request $request,
        protected QueryBuilder $queryBuilder,
        protected string $entityName,
        protected array $params,
    ) {
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
