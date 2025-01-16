<?php

namespace App\Http\Api\Controller;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route(path: '/category/search', name: 'category_search')]
    public function search(Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        $search = $request->query->get('q');
        if (null === $search) {
            return $this->json([]);
        }
        $categories = $categoryRepository->searchByName($search);

        return $this->json(array_map(fn (Category $category): array => [
            'name' => $category->getName(),
            'id' => $category->getId(),
        ], $categories));
    }
}
