<?php

namespace App\Http\Admin\Controller;

use App\Domain\Recipe\Recipe;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(name: 'recipe_')]
class RecipeController extends CrudController
{
    protected string $entity = Recipe::class;
    protected string $templatePath = 'recipe';
    protected string $routePrefix = 'admin_recipe';

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        return $this->crudIndex();
    }
}
