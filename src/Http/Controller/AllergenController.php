<?php

namespace App\Http\Controller;

use App\Domain\Allergen\Allergen;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/allergen', name: 'allergen_')]
class AllergenController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $letters = range('A', 'Z');
        $allergensByLetters = $this->em->getRepository(Allergen::class)->findAllergensByLetters();

        return $this->render('allergens/index.html.twig', [
            'letters' => $letters,
            'allergensByLetters' => $allergensByLetters,
            'menuItem' => 'allergen'
        ]);
    }
}
