<?php

namespace App\Http\Admin\Controller;

use App\Domain\Recipe\Recipe;
use App\Http\Admin\Form\RecipeForm;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @extends CrudController<Recipe>
 */
#[Route(name: 'recipe_')]
class RecipeController extends CrudController
{
    protected string $entity = Recipe::class;

    protected ?string $templatePath = 'recipe';

    protected ?string $routePrefix = 'admin_recipe';

    protected ?string $menuItem = 'recipe';

    protected ?string $searchField = 'name';

    protected string $formClass = RecipeForm::class;

    /** @var array{'update': ?string, 'delete': ?string, 'create': ?string} */
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(Request $request): Response
    {
        return $this->crudNew((new Recipe())->setUser($this->getUser()));
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Recipe $recipe): Response
    {
        return $this->crudEdit($recipe);
    }

    #[Route(path: '/delete/{id<\d+>}', name: 'delete', methods: ['GET'])]
    public function delete(Recipe $recipe): RedirectResponse
    {
        return $this->crudDelete($recipe);
    }
}
