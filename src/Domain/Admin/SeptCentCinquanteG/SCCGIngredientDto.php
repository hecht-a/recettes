<?php

namespace App\Domain\Admin\SeptCentCinquanteG;

class SCCGIngredientDto
{
    public int $weight;

    public ServingDto $serving;

    /** @var RecipeRawgredientDto[] */
    public array $recipeRawgredients = [];
}
