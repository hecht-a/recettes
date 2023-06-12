<?php

namespace App\Http\Admin\Controller;

use App\Domain\Category\Category;
use App\Domain\Ingredient\Ingredient;
use App\Http\Admin\Form\CategoryForm;
use App\Http\Admin\Form\IngredientForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories', name: 'category_')]
class CategoryController extends AbstractController
{
    protected string $entity = Category::class;

    protected string $templatePath = 'category';

    protected string $routePrefix = 'admin_category';

    protected string $menuItem = 'category';

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    private array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    private string $formClass = CategoryForm::class;

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $query = $this->getRepository()
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

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(Request $request): Response
    {
        $entity = new Category();

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

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm($this->formClass, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $old = clone $category;
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($category, $old));
            }
            $this->addFlash('success', [
                'title' => 'Succès',
                'description' => 'Le contenu a bien été modifié',
            ]);

            return $this->redirectAfterSave($category);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form' => $form->createView(),
            'entity' => $category,
            'menuItem' => $this->menuItem,
        ]);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Category $category): RedirectResponse
    {
        $this->em->remove($category);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($category));
        }
        $this->em->flush();
        $this->addFlash('success', [
            'title' => 'Succès',
            'description' => 'Le contenu a bien été supprimé',
        ]);

        return $this->redirectToRoute($this->routePrefix.'_index');
    }
}
