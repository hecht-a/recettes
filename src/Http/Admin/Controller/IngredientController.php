<?php

namespace App\Http\Admin\Controller;

use App\Domain\Ingredient\Ingredient;
use App\Http\Admin\Form\IngredientForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/ingredients', name: 'ingredient_')]
class IngredientController extends CrudController
{
    protected string $entity = Ingredient::class;

    protected ?string $templatePath = 'ingredient';

    protected ?string $routePrefix = 'admin_ingredient';

    protected ?string $menuItem = 'ingredient';

    protected ?string $searchField = 'name';

    protected string $formClass = IngredientForm::class;

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
    public function new(): Response
    {
        return $this->crudNew(new Ingredient());
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Ingredient $ingredient): Response
    {
        return $this->crudEdit($ingredient);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Ingredient $ingredient): RedirectResponse
    {
        return $this->crudDelete($ingredient);
    }
}
