<?php

namespace App\Http\Controller;

use App\Domain\Ingredient\Ingredient;
use App\Domain\Utensil\Utensil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/ingredients', name: 'ingredient_')]
class IngredientController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $letters = range('A', 'Z');
        $ingredientsByLetters = $this->em->getRepository(Ingredient::class)->findIngredientsByLetters();

        return $this->render('ingredients/index.html.twig', [
            'letters' => $letters,
            'ingredientsByLetters' => $ingredientsByLetters,
            'menuItem' => 'ingredient',
        ]);
    }

    #[Route('/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(Ingredient $ingredient, string $slug): Response
    {
        if ($ingredient->getSlug() !== $slug) {
            return $this->redirectToRoute('ingredient_show', [
                'id' => $ingredient->getId(),
                'slug' => $ingredient->getSlug(),
            ], 301);
        }

        return $this->render('ingredients/show.html.twig', [
            'menuItem' => 'ingredient',
            'ingredient' => $ingredient,
        ]);
    }
}
