<?php declare(strict_types=1);

namespace App\Http\Controller;

use App\Domain\Recipe\RecipeRepository;
use App\Domain\ShoppingList\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profile', name: 'profile_')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, private readonly RecipeRepository $recipeRepository)
    {
        parent::__construct($em);
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('profile/index.html.twig', [
            'favorites' => $this->recipeRepository->findFavoritesQuery($this->getUserOrThrow())->getQuery()->getResult()
        ]);
    }

    #[Route('/shopping-list/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}', name: 'show')]
    public function show(ShoppingList $shoppingList, string $slug): Response
    {
        if ($shoppingList->getSlug() !== $slug) {
            return $this->redirectToRoute('profile_shopping_list', ['slug' => $shoppingList->getSlug(), 'id' => $shoppingList->getId()]);
        }

        return $this->render('profile/show.html.twig', [
            'shoppingList' => $shoppingList,
        ]);
    }
}
