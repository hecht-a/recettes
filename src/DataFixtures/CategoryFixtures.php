<?php

namespace App\DataFixtures;

use App\Domain\Category\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Rapido', 'description' => 'Rapido'],
            ['name' => 'Végétarien', 'description' => 'Végétarien'],
            ['name' => 'Familial', 'description' => 'Familial'],
            ['name' => 'Eco', 'description' => 'Eco'],
            ['name' => 'Apéritif', 'description' => 'Apéritif'],
            ['name' => 'Dessert', 'description' => 'Dessert'],
            ['name' => 'Petit-déjeuner', 'description' => 'Petit-déjeuner'],
            ['name' => 'Végétalien', 'description' => 'Végétalien'],
            ['name' => 'Cuisine du monde', 'description' => 'Cuisine du monde'],
        ];

        CategoryFactory::createSequence($categories);
        $manager->flush();
    }
}
