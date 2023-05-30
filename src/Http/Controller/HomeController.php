<?php

namespace App\Http\Controller;

use App\Domain\Recipe\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route(path: '/', name: 'home')]
    public function index(): Response
    {
        $recipes = $this->em->getRepository(Recipe::class)->findAll();
        dd($recipes);

        return $this->render('pages/home.html.twig', [
            'recipes' => $recipes
        ]);
    }
}
