<?php

namespace App\DataFixtures;

use App\Domain\Recipe\RecipeFactory;
use App\Domain\Step\StepFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StepFixtures extends Fixture implements DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $recipes = RecipeFactory::randomRange(5, 10);

        foreach ($recipes as $recipe) {
            $stepCount = mt_rand(3, 7);
            for ($i = 1; $i <= $stepCount; ++$i) {
                StepFactory::createOne([
                    'recipe' => $recipe,
                    'description' => "Étape $i pour préparer la recette " . $recipe->getName(),
                    'position' => $i,
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
        ];
    }
}
