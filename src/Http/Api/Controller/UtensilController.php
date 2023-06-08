<?php

namespace App\Http\Api\Controller;

use App\Domain\Utensil\Utensil;
use App\Domain\Utensil\UtensilRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UtensilController extends AbstractController
{
    #[Route(path: '/utensil/search', name: 'utensil_search')]
    public function search(Request $request, UtensilRepository $utensilRepository): JsonResponse
    {
        $search = $request->query->get('q');
        if (null === $search) {
            return $this->json([]);
        }
        $utensils = $utensilRepository->searchByName($search);

        return $this->json(array_map(fn (Utensil $utensil) => [
            'name' => $utensil->getName(),
            'id' => $utensil->getId(),
        ], $utensils));
    }
}
