<?php

namespace App\Http\Admin\Controller;

use App\Domain\Recipe\Recipe;
use App\Helper\Paginator\PaginatorInterface;
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
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    public function __construct(
        EntityManagerInterface $em,
        protected PaginatorInterface $paginator,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly RequestStack $requestStack
    ) {
        parent::__construct($em);
    }

    public function crudIndex(QueryBuilder $query = null, array $extraParams = []): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');
        $this->paginator->allowSort('row.id', 'row.title');
        $rows = $this->paginator->paginate($query->getQuery());

        dd($rows);

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
            'searchable' => true,
            'prefix' => $this->routePrefix,
            ...$extraParams,
        ]);
    }

    public function crudEdit(): Response
    {
        return $this->render("admin/{$this->templatePath}/edit.html.twig");
    }

    public function crudNew(): Response
    {
        return $this->render("admin/{$this->templatePath}/new.html.twig");
    }

    public function crudDelete(object $entity, ?string $redirectRoute = null): RedirectResponse
    {
        $this->em->remove($entity);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($entity));
        }
        $this->em->flush();
        $this->addFlash('success', 'Le contenu a bien été supprimé');

        return $this->redirectToRoute($redirectRoute ?: ($this->routePrefix.'_index'));
    }

    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entity);
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
