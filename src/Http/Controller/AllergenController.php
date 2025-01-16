<?php

namespace App\Http\Controller;

use App\Domain\Allergen\Allergen;
use App\Domain\Allergen\AllergenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/allergen', name: 'allergen_')]
class AllergenController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, private readonly AllergenRepository $allergenRepository)
    {
        parent::__construct($em);
    }

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $letters = range('A', 'Z');
        $allergensByLetters = $this->allergenRepository->findAllergensByLetters();

        return $this->render('allergens/index.html.twig', [
            'letters' => $letters,
            'allergensByLetters' => $allergensByLetters,
            'menuItem' => 'allergen',
        ]);
    }

    #[Route('/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(Allergen $allergen, string $slug): Response
    {
        if ($allergen->getSlug() !== $slug) {
            return $this->redirectToRoute('allergen_show', [
                'id' => $allergen->getId(),
                'slug' => $allergen->getSlug(),
            ], 301);
        }

        return $this->render('allergens/show.html.twig', [
            'menuItem' => 'allergen',
            'allergen' => $allergen,
        ]);
    }
}
