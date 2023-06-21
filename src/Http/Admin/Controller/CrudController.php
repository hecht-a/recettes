<?php

namespace App\Http\Admin\Controller;

use App\Domain\Auth\User;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Controller\AbstractController;
use App\Infra\Admin\MissingPropertyException;
use App\Infra\Interfaces\IdentifiableInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method User getUser()
 *
 * @template E of object implementing IdentifiableInterface
 */
abstract class CrudController extends AbstractController
{
    protected bool $indexOnSave = false;

    /**
     * @var class-string<E>
     */
    protected string $entity;

    protected ?string $templatePath = null;

    protected ?string $routePrefix = null;

    protected ?string $menuItem = null;

    protected ?string $searchField = null;

    protected string $formClass = '';

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    public function __construct(
        EntityManagerInterface $em,
        protected readonly PaginatorInterface $paginator,
        protected readonly EventDispatcherInterface $dispatcher,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($em);
    }

    /**
     * @throws MissingPropertyException
     */
    public function isValidCrudController(): void
    {
        if (isset($this->entity)) {
            throw new MissingPropertyException(self::class, 'entity');
        }
        if ($this->templatePath === null) {
            throw new MissingPropertyException(self::class, 'templatePath');
        }
        if ($this->routePrefix === null) {
            throw new MissingPropertyException(self::class, 'routePrefix');
        }
        if ($this->menuItem === null) {
            throw new MissingPropertyException(self::class, 'menuItem');
        }
        if ($this->searchField === null) {
            throw new MissingPropertyException(self::class, 'searchField');
        }
        if ($this->formClass === '') {
            throw new MissingPropertyException(self::class, 'formClass');
        }
    }

    /**
     * @throws MissingPropertyException
     */
    public function crudIndex(?QueryBuilder $query = null): Response
    {
        $this->isValidCrudController();

        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row');

        if ($request->get('q')) {
            $query = $this->applySearch(trim((string) $request->get('q')), $query);
        }

        $rows = $this->paginator->paginate($query->getQuery());

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
            'searchable' => true,
            'prefix' => $this->routePrefix,
            'menuItem' => $this->menuItem,
        ]);
    }

    /**
     * @param E&IdentifiableInterface $entity
     *
     * @throws MissingPropertyException
     */
    public function crudNew(object $entity): Response
    {
        $this->isValidCrudController();

        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm($this->formClass, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($entity);
            $this->em->flush();
            if ($this->events['create'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['create']($entity));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été créé',
            ]);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/{$this->templatePath}/new.html.twig", [
            'form' => $form->createView(),
            'entity' => $entity,
            'menuItem' => $this->menuItem,
        ]);
    }

    /**
     * @param E&IdentifiableInterface $entity
     *
     * @throws MissingPropertyException
     */
    public function crudEdit(object $entity): Response
    {
        $this->isValidCrudController();

        $request = $this->requestStack->getCurrentRequest();

        $form = $this->createForm($this->formClass, $entity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old = clone $entity;
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($entity, $old));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été modifié',
            ]);

            return $this->redirectAfterSave($entity);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $entity,
            'menuItem' => $this->menuItem,
        ]);
    }

    /**
     * @param E&IdentifiableInterface $entity
     *
     * @throws MissingPropertyException
     */
    public function crudDelete(object $entity): RedirectResponse
    {
        $this->isValidCrudController();

        $this->em->remove($entity);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($entity));
        }
        $this->em->flush();
        $this->addFlash('success', [
            'title' => 'Succès',
            'description' => 'Le contenu a bien été supprimé',
        ]);

        return $this->redirectToRoute($this->routePrefix.'_index');
    }

    /**
     * @return EntityRepository<E>
     */
    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entity);
    }

    protected function applySearch(string $search, QueryBuilder $query): QueryBuilder
    {
        return $query
            ->where("LOWER(row.{$this->searchField}) LIKE :search")
            ->setParameter('search', '%'.strtolower($search).'%');
    }

    /**
     * @param E&IdentifiableInterface $entity
     */
    protected function redirectAfterSave(object $entity): RedirectResponse
    {
        if ($this->indexOnSave) {
            return $this->redirectToRoute($this->routePrefix.'_index');
        }

        return $this->redirectToRoute($this->routePrefix.'_edit', ['id' => $entity->getId()]);
    }
}
