<?php

namespace App\Http\Api\Controller;

use App\Domain\Recipe\Recipe;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FavoriteController extends AbstractController
{
    #[Route(path: '/like/{id<\d+>}', name: 'like')]
    #[IsGranted('ROLE_USER')]
    public function like(Recipe $recipe): JsonResponse
    {
        $recipe->addUserFavorite($this->getUser());
        $this->em->flush();

        return new JsonResponse([
            'status' => 'liked'
        ]);
    }

    #[Route(path: '/dislike/{id<\d+>}', name: 'dislike')]
    #[IsGranted('ROLE_USER')]
    public function dislike(Recipe $recipe): JsonResponse
    {
        $recipe->removeUserFavorite($this->getUser());
        $this->em->flush();

        return new JsonResponse([
            'status' => 'disliked'
        ]);
    }
}
