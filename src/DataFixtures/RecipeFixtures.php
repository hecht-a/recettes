<?php

namespace App\DataFixtures;

use App\Domain\Allergen\AllergenFactory;
use App\Domain\Auth\UserFactory;
use App\Domain\Category\CategoryFactory;
use App\Domain\Recipe\RecipeFactory;
use App\Domain\Utensil\UtensilFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $users = UserFactory::randomRange(2, 5);

        for ($i = 0; $i < 10; ++$i) {
            RecipeFactory::createOne([
                'categories' => CategoryFactory::randomRange(1, 3),
                'utensils' => UtensilFactory::randomRange(1, 3),
                'allergens' => AllergenFactory::randomRange(3, 5),
                'user' => $users[array_rand($users)],
            ]);
        }

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            AllergenFixtures::class,
            CategoryFixtures::class,
            UtensilFixtures::class,
            UserFixtures::class,
        ];
    }
}
