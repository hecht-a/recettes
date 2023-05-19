<?php

namespace App\Domain\IngredientRecipe;

use App\Domain\Ingredient\Ingredient;
use App\Domain\Recipe\Recipe;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IngredientRecipeRepository::class)]
#[ORM\Table(name: 'ingredient_recipe')]
class IngredientRecipe
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'recipes')]
    private ?Ingredient $ingredient;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Recipe::class, inversedBy: 'ingredients')]
    private ?Recipe $recipe;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    private float $quantity;

    /**
     * @return Ingredient|null
     */
    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    /**
     * @param Ingredient|null $ingredient
     * @return IngredientRecipe
     */
    public function setIngredient(?Ingredient $ingredient): IngredientRecipe
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    /**
     * @return Recipe|null
     */
    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    /**
     * @param Recipe|null $recipe
     * @return IngredientRecipe
     */
    public function setRecipe(?Recipe $recipe): IngredientRecipe
    {
        $this->recipe = $recipe;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     * @return IngredientRecipe
     */
    public function setQuantity(float $quantity): IngredientRecipe
    {
        $this->quantity = $quantity;

        return $this;
    }
}
