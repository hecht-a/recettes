<?php

namespace App\DataFixtures;

use App\Domain\Ingredient\IngredientFactory;
use App\Domain\IngredientRecipe\IngredientRecipeFactory;
use App\Domain\Recipe\RecipeFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IngredientRecipeFixtures extends Fixture implements DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $recipes = RecipeFactory::randomRange(5, 10);

        foreach ($recipes as $recipe) {
            $ingredients = IngredientFactory::randomRange(10, 15);

            foreach ($ingredients as $ingredient) {
                IngredientRecipeFactory::createOne([
                    'recipe' => $recipe,
                    'ingredient' => $ingredient,
                    'quantity' => mt_rand(50, 500) / 10,
                ]);
            }
        }

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            RecipeFixtures::class,
            IngredientFixtures::class,
        ];
    }
}
