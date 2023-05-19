<?php

namespace App\Domain\IngredientRecipe;

use App\Domain\Ingredient\Ingredient;
use App\Domain\Recipe\Recipe;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRecipeRepository::class)]
#[ORM\Table(name: 'ingredient_recipe')]
class IngredientRecipe
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'recipes')]
    private Ingredient $ingredient;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'ingredients')]
    private Recipe $recipe;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    private float $quantity;

    public function getIngredient(): Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(Ingredient $ingredient): IngredientRecipe
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(Recipe $recipe): IngredientRecipe
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): IngredientRecipe
    {
        $this->quantity = $quantity;

        return $this;
    }
}
