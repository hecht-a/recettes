<?php

namespace App\Domain\ShoppingListRecipe;

use App\Domain\Recipe\Recipe;
use App\Domain\ShoppingList\ShoppingList;
use App\Infra\DateableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListRecipeRepository::class)]
class ShoppingListRecipe
{
    use DateableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ShoppingList::class, cascade: ['remove'], inversedBy: 'recipes')]
    private ?ShoppingList $shoppingList = null;

    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'shoppingListRecipes')]
    private ?Recipe $recipe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): void
    {
        $this->shoppingList = $shoppingList;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): void
    {
        $this->recipe = $recipe;
    }
}
