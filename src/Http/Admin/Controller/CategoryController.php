<?php

namespace App\Http\Admin\Controller;

use App\Domain\Category\Category;
use App\Http\Admin\Form\CategoryForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/categories', name: 'category_')]
class CategoryController extends CrudController
{
    protected string $entity = Category::class;

    protected ?string $templatePath = 'category';

    protected ?string $routePrefix = 'admin_category';

    protected ?string $menuItem = 'category';

    protected ?string $searchField = 'name';

    protected string $formClass = CategoryForm::class;

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(Request $request): Response
    {
        return $this->crudNew(new Category());
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Category $category): Response
    {
        return $this->crudEdit($category);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Category $category): RedirectResponse
    {
        return $this->crudDelete($category);
    }
}
