<?php

namespace App\Http\Controller;

use App\Domain\Recipe\Recipe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/recipes', name: 'recipes_')]
class RecipeController extends AbstractController
{
    #[Route(path: '/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}', name: 'show')]
    public function show(Recipe $recipe, string $slug): Response
    {
        if ($recipe->getSlug() !== $slug) {
            return $this->redirectToRoute('recipes_show', [
                'id' => $recipe->getId(),
                'slug' => $recipe->getSlug(),
            ], 301);
        }

        return $this->render('recipes/show.html.twig', [
            'recipe' => $recipe,
        ]);
    }
}
