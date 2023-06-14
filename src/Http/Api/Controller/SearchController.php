<?php

namespace App\Http\Api\Controller;

use App\Domain\Category\Category;
use App\Domain\Recipe\Recipe;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class SearchController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, private readonly SerializerInterface $serializer, private readonly UploaderHelper $helper)
    {
        parent::__construct($em);
    }

    #[Route(path: '/search', name: 'search')]
    public function search(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        if(empty($q)) {
            return $this->json([]);
        }

        $recipes = $this->em->getRepository(Recipe::class)->searchByName($q);
        $recipesMatches = array_map(fn (Recipe $recipe) => [
            'title' => $recipe->getName(),
            'url' => $this->serializer->serialize($recipe, 'path'),
            'categories' => collect($recipe->getCategories())->map(fn(Category $category) => $category->getName())->join(', '),
            'image' => $this->helper->asset($recipe)
        ], $recipes);

        return $this->json($recipesMatches);
    }
}
