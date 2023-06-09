<?php

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/allergen', name: 'allergen_')]
class AllergenController extends AbstractController
{
    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        return $this->render('allergens/index.html.twig', [
            'menuItem' => 'allergen'
        ]);
    }
}
