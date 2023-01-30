<?php

declare(strict_types=1);

namespace App\Service;

use App\Constants\DatatableConstants;
use App\Entity\Parameter\ParameterKey;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Event\Datatable\DatatableCreateQueryEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack as RequestFW;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Twig\Environment;

class Datatable
{
    private Request $request;
    private EntityManagerInterface $em;
    private Environment $twig;

    public function __construct(
        RequestFW $request,
        EntityManagerInterface $em,
        Environment $twig,
        private EventDispatcherInterface $dispatcher,
    ) {
        $this->request = $request->getCurrentRequest();
        $this->em = $em;
        $this->twig = $twig;
    }

    public function has(string $key): bool
    {
        return $this->request->attributes->has($key) || $this->request->request->has($key) || $this->request->query->has($key);
    }

    public function get(string $key, string $default = null): string
    {
        return $this->request->get($key, $default);
    }

    public function filter(string $key, int|Datatable $default = null, int $filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS, array $options = []): Datatable|string|array|int|null
    {
        foreach ([$this->request->attributes, $this->request->request, $this->request->query] as $obj) {
            if ($obj->has($key)) {
                $result = $obj->filter($key, $this, $filter, $options);
                if ($this !== $result) {
                    return $result;
                }
            }
        }

        return $default;
    }

    public function cleanEmptyArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if ($value === null or $value === '') {
                unset($array[$key]);
            }
        }

        return $array;
    }

    public function dataTableOrderBy(array $columns, string $key, ?array $default = null): ?array
    {
        $result = $this->filter(
            $key,
            $this,
            FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            ['flags' => FILTER_NULL_ON_FAILURE | FILTER_REQUIRE_ARRAY]
        );
        if ($result !== $this && is_array($result)) {
            $orderBy = [];
            $columns = array_values($columns);
            foreach ($result as $order) {
                if (!key_exists('dir', $order) or !key_exists('column', $order)) {
                    continue;
                }
                if (!in_array(strtolower($order['dir']), ['asc', 'desc'], true)) {
                    continue;
                }
                $index = (int)$order['column'];
                if (!isset($columns[$index]) || empty($columns[$index]['db'])) {
                    continue;
                }
                $orderBy[$columns[$index]['db']] = $order['dir'];
            }
            if (count($orderBy) > 0) {
                return $orderBy;
            }
        }

        return $default;
    }

    public function countFromQuery(QueryBuilder $query, ?string $countSelect = 'count(a.id)', ?bool $withWhere = null): int
    {
        if (null == $withWhere) {
            $withWhere = false;
        }
        $query = clone $query;
        if (!$withWhere) {
            $query->resetDQLPart('where');
            $query->setParameters(new ArrayCollection());
        }
        $query->resetDQLPart('select');
        $query->resetDQLPart('orderBy');
        $query->setMaxResults(1);
        $query->setFirstResult(0);
        $query->select($countSelect);
        try {
            $maxRowFiltered = $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            $maxRowFiltered = 0;
        }

        return $maxRowFiltered;
    }

    public function getDatatableResponse(
        Request $request,
        string $entityName,
        array $data,
        string $pathTemplate,
        array $additionalArgs = []
    ): Response {
        $parameterKey = $this->em->getRepository(ParameterKey::class)->findOneBy(['name' => 'maxResultsDatatablePerPage']);
        $paginationNb = $parameterKey?->getParameters()[0]?->getValue();

        $start = strval($this->filter('start', DatatableConstants::TABLE_PAGINATION_START, FILTER_SANITIZE_NUMBER_INT));
        $length = strval($this->filter('length', $paginationNb, FILTER_SANITIZE_NUMBER_INT));
        $data['columns']['action'] = ['label' => "Actions"];
        $order = $this->dataTableOrderBy($data['columns'], 'order');

        if (in_array('application/json', $request->getAcceptableContentTypes(), true)) {

            /** @phpstan-ignore-next-line */
            $repository = $this->em->getRepository($entityName);

            $queryBuilder = $repository->createQueryBuilder('a');

            // On dispatch un evenement pour pourvoir modifier la requete avant les filtres de datatables
            $event = new DatatableCreateQueryEvent(
                request: $request,
                queryBuilder: $queryBuilder,
                entityName: $entityName,
                params: $additionalArgs,
            );
            $this->dispatcher->dispatch($event, DatatableCreateQueryEvent::NAME);

            $query = $repository->filterQueryBuilder(
                $queryBuilder,
                $data,
                (int)$length,
                (int)$start,
                $order
            );
            $paginator = new Paginator($query);
            $return = [];

            $slugger = new AsciiSlugger();
            foreach ($paginator as $item) {
                $subData = [];

                $subData['DT_RowId'] = $slugger->slug(strtolower(
                    $this->twig->render(sprintf('%s/datatable/_DT_RowId.html.twig', $pathTemplate), ['item' => $item])
                ));
                foreach ($data['columns'] as $key => $value) {
                    $render = null;
                    if ($this->twig->getLoader()->exists(sprintf('%s/datatable/_%s.html.twig', $pathTemplate, $key))) {
                        $render = $this->twig->render(sprintf('%s/datatable/_%s.html.twig', $pathTemplate, $key), ['item' => $item]);
                    }
                    $subData[$key] = $render ?? '';
                }

                $return[] = $subData;
            }

            return new JsonResponse([
                "draw"            => $this->get('draw'),
                "recordsTotal"    => $this->countFromQuery($query, 'count(a.id)', false),
                "recordsFiltered" => $this->countFromQuery($query, 'count(a.id)', true),
                "data"            => $return
            ]);
        }

        $arguments = [
            'start'   => $start,
            'length'  => $length,
            'columns' => $data['columns'],
            'params'  => $data['filters'],
        ];
        if (!empty($additionalArgs)) {
            $arguments = array_merge($arguments, $additionalArgs);
        }
        return new Response($this->twig->render(sprintf('%s/%s', $pathTemplate, 'index.html.twig'), $arguments));
    }
}
