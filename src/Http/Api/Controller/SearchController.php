<?php

namespace App\Http\Api\Controller;

use App\Domain\Category\Category;
use App\Domain\Recipe\Recipe;
use App\Domain\Recipe\RecipeRepository;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SearchController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, private readonly SerializerInterface $serializer, private readonly UploaderHelper $helper, private readonly RecipeRepository $recipeRepository)
    {
        parent::__construct($em);
    }

    #[Route(path: '/search', name: 'search')]
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        if ('' === $q || '0' === $q) {
            return $this->json([]);
        }

        $recipes = $this->recipeRepository->searchByName($q);
        $recipesMatches = array_map(fn (Recipe $recipe): array => [
            'title' => $recipe->getName(),
            'url' => $this->serializer->serialize($recipe, 'path'),
            'categories' => collect($recipe->getCategories())->map(fn (Category $category): ?string => $category->getName())->join(', '),
            'image' => $this->helper->asset($recipe),
        ], $recipes);

        return $this->json($recipesMatches);
    }
}
