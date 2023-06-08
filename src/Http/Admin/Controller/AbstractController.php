<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\User;
use App\Domain\Recipe\Recipe;
use App\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @method User|null getUser()
 */
class AbstractController extends \App\Http\Controller\AbstractController
{
    protected bool $indexOnSave = false;

    protected string $routePrefix = "";

    /**
     * @var class-string<E>
     */
    protected string $entity = Recipe::class;

    protected string $searchField = 'name';

    public function __construct(
        EntityManagerInterface $em,
        protected PaginatorInterface $paginator,
        protected readonly EventDispatcherInterface $dispatcher
    ) {
        parent::__construct($em);
    }

    /**
     * @return EntityRepository<E>
     */
    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entity); /* @phpstan-ignore-line */
    }

    protected function applySearch(string $search, QueryBuilder $query): QueryBuilder
    {
        return $query
            ->where("LOWER(row.{$this->searchField}) LIKE :search")
            ->setParameter('search', '%'.strtolower($search).'%');
    }

    protected function redirectAfterSave($entity): RedirectResponse
    {
        if ($this->indexOnSave) {
            return $this->redirectToRoute($this->routePrefix . '_index');
        }

        return $this->redirectToRoute($this->routePrefix . '_edit', ['id' => $entity->getId()]);
    }
}
