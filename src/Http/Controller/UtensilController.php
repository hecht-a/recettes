<?php

namespace App\Http\Controller;

use App\Domain\Utensil\Utensil;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/utensils', name: 'utensil_')]
class UtensilController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $letters = range('A', 'Z');
        $utensilsByLetters = $this->em->getRepository(Utensil::class)->findUtensilsByLetters();

        return $this->render('utensils/index.html.twig', [
            'letters' => $letters,
            'utensilsByLetters' => $utensilsByLetters,
            'menuItem' => 'utensil',
        ]);
    }

    #[Route('/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(Utensil $utensil, string $slug): Response
    {
        if ($utensil->getSlug() !== $slug) {
            return $this->redirectToRoute('utensil_show', [
                'id' => $utensil->getId(),
                'slug' => $utensil->getSlug(),
            ], 301);
        }

        return $this->render('utensils/show.html.twig', [
            'menuItem' => 'utensil',
            'utensil' => $utensil,
        ]);
    }
}
