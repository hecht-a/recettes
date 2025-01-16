<?php

namespace App\Helper\Paginator;

use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Implémentation basée sur KnpPaginatorBundle.
 */
class Paginator implements PaginatorInterface
{
    /** @var string[] */
    private array $sortableFields = [];

    public function __construct(
        private readonly \Knp\Component\Pager\PaginatorInterface $paginator,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return PaginationInterface<mixed>
     */
    #[\Override]
    public function paginate(Query $query): PaginationInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        $page = $request instanceof Request ? $request->query->getInt('page', 1) : 1;

        if ($page <= 0) {
            throw new PageOutOfBoundException();
        }

        return $this->paginator->paginate($query, $page, $query->getMaxResults() ?: 15, [
            'sortFieldWhitelist' => $this->sortableFields,
            'filterFieldWhitelist' => [],
        ]);
    }

    #[\Override]
    public function allowSort(string ...$fields): self
    {
        $this->sortableFields = array_merge($this->sortableFields, $fields);

        return $this;
    }
}
