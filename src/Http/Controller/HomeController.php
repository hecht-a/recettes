<?php

namespace App\Http\Controller;

use App\Domain\Recipe\Recipe;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        $recipes = $this->em->getRepository(Recipe::class)->findAll();

        return $this->render('pages/home.html.twig', [
            'recipes' => $recipes
        ]);
    }
}
