<?php

namespace App\Http\Twig\Components\Profile;

use App\Domain\Auth\User;
use App\Domain\Recipe\Recipe;
use App\Domain\Recipe\RecipeRepository;
use App\Domain\ShoppingList\ShoppingList;
use App\Domain\ShoppingList\ShoppingListRepository;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class Tabs extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public string $tab = 'favorites';

    /** @var Recipe[] */
    #[LiveProp(writable: true)]
    public array $favorites = [];

    /** @var ShoppingList[] */
    #[LiveProp(writable: true)]
    public array $shoppingLists = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ShoppingListRepository $shoppingListRepository,
        private readonly RecipeRepository       $recipeRepository
    ) {
        parent::__construct($entityManager);
    }

    #[LiveAction]
    public function switchTab(#[LiveArg] string $tab): void
    {
        $this->tab = $tab;

        if($tab === 'favorites') {
            $this->favorites = $this->recipeRepository->findFavoritesQuery($this->getUserOrThrow())->getQuery()->getResult();
        } else {
            $this->shoppingLists = $this->shoppingListRepository->findBy(['user' => $this->getUserOrThrow()]);
        }
    }
}
