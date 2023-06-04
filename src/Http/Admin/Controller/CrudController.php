<?php

namespace App\Http\Admin\Controller;

use App\Domain\Recipe\Recipe;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Admin\Data\CrudDataInterface;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template E
 *
 * @method \App\Domain\Auth\User getUser()
 */
abstract class CrudController extends AbstractController
{
    /**
     * @var class-string<E>
     */
    protected string $entity = Recipe::class;
    protected string $templatePath = 'recipe';
    protected string $routePrefix = '';
    protected bool $indexOnSave = true;

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string}  */
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];
    protected string $searchField = 'name';

    public function __construct(
        EntityManagerInterface $em,
        protected PaginatorInterface $paginator,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($em);
    }

    /**
     * @param QueryBuilder|null $query
     * @param string[] $extraParams
     * @return Response
     */
    public function crudIndex(QueryBuilder $query = null, array $extraParams = []): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');

        if ($request->get('q')) {
            $query = $this->applySearch(trim((string) $request->get('q')), $query);
        }

        $rows = $this->paginator->paginate($query->getQuery());

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
            'searchable' => true,
            'prefix' => $this->routePrefix,
            ...$extraParams,
        ]);
    }

    public function crudEdit(CrudDataInterface $data): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var E $entity */
            $entity = $data->getEntity();
            $old = clone $entity;
            $data->hydrate();
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($entity, $old));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été modifié'
            ]);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $data->getEntity()
        ]);
    }

    public function crudNew(CrudDataInterface $data): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var E $entity */
            $entity = $data->getEntity();
            $data->hydrate();
            $this->em->persist($entity);
            $this->em->flush();
            if ($this->events['create'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['create']($data->getEntity()));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été créé'
            ]);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/{$this->templatePath}/new.html.twig", [
            'form' => $form->createView(),
            'entity' => $data->getEntity()
        ]);
    }

    public function crudDelete(object $entity, ?string $redirectRoute = null): RedirectResponse
    {
        $this->em->remove($entity);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($entity));
        }
        $this->em->flush();
        $this->addFlash('success', [
            'title' => 'Succès',
            'description' => 'Le contenu a bien été supprimé'
        ]);

        return $this->redirectToRoute($redirectRoute ?: ($this->routePrefix.'_index'));
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

    /**
     * @param E $entity
     */
    protected function redirectAfterSave($entity): RedirectResponse
    {
        if ($this->indexOnSave) {
            return $this->redirectToRoute($this->routePrefix.'_index');
        }

        return $this->redirectToRoute($this->routePrefix.'_edit', ['id' => $entity->getId()]);
    }
}
