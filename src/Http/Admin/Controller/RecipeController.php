<?php

namespace App\Http\Admin\Controller;

use App\Domain\Category\Category;
use App\Domain\Recipe\Recipe;
use App\Http\Admin\Data\RecipeCrudData;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'recipe_')]
class RecipeController extends CrudController
{
    protected string $entity = Recipe::class;
    protected string $templatePath = 'recipe';
    protected string $routePrefix = 'admin_recipe';

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->crudIndex();
    }

    #[Route(path: '/new', name: 'new', methods: ['POST', 'GET'])]
    public function new(): Response
    {
        $entity = (new Recipe())->setUser($this->getUser());
        $data = new RecipeCrudData($entity);

        return $this->crudNew($data);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, Recipe $recipe): Response
    {
        $data = (new RecipeCrudData($recipe))->setEntityManager($this->em);
        return $this->crudEdit($data);
    }
}
