<?php

namespace App\Http\Api\Controller;

use App\Domain\Allergen\Allergen;
use App\Domain\Allergen\AllergenRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AllergenController extends AbstractController
{
    #[Route(path: '/allergen/search', name: 'allergen_search')]
    public function search(Request $request, AllergenRepository $allergenRepository): JsonResponse
    {
        $search = $request->query->get('q');
        if (null === $search) {
            return $this->json([]);
        }
        $allergens = $allergenRepository->searchByName($search);

        return $this->json(array_map(fn (Allergen $allergen): array => [
            'name' => $allergen->getName(),
            'id' => $allergen->getId(),
        ], $allergens));
    }
}
