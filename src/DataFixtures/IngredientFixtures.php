<?php

namespace App\DataFixtures;

use App\Domain\Ingredient\IngredientFactory;
use App\Domain\Unit\UnitFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $units = [
            'g' => UnitFactory::find(['shortLabel' => 'g']),
            'ml' => UnitFactory::find(['shortLabel' => 'ml']),
            'u' => UnitFactory::find(['shortLabel' => 'u']),
        ];

        $ingredients = [
            ['name' => 'Tomate', 'description' => 'Fruit souvent utilisé comme légume, incontournable dans les salades et les sauces.', 'unit' => $units['u']],
            ['name' => 'Carotte', 'description' => 'Légume-racine riche en bêta-carotène, souvent consommé cru ou cuit.', 'unit' => $units['u']],
            ['name' => 'Poulet', 'description' => 'Viande blanche populaire, riche en protéines et peu grasse.', 'unit' => $units['g']],
            ['name' => 'Riz', 'description' => 'Céréale de base dans de nombreuses cultures culinaires.', 'unit' => $units['g']],
            ['name' => 'Pomme de terre', 'description' => 'Tubercule polyvalent, utilisé pour les frites, purées ou soupes.', 'unit' => $units['g']],
            ['name' => 'Oignon', 'description' => 'Aromate essentiel pour de nombreuses recettes, utilisé cru ou cuit.', 'unit' => $units['u']],
            ['name' => 'Ail', 'description' => 'Aromate puissant, souvent utilisé pour relever les plats.', 'unit' => $units['u']],
            ['name' => 'Basilic', 'description' => 'Herbe aromatique emblématique de la cuisine italienne.', 'unit' => $units['g']],
            ['name' => 'Thon', 'description' => 'Poisson souvent utilisé en conserve ou frais dans des plats comme les salades.', 'unit' => $units['g']],
            ['name' => 'Crevettes', 'description' => 'Crustacés populaires dans les plats asiatiques et méditerranéens.', 'unit' => $units['g']],
            ['name' => 'Farine', 'description' => 'Ingrédient de base pour la pâtisserie et la boulangerie.', 'unit' => $units['g']],
            ['name' => 'Beurre', 'description' => 'Produit laitier utilisé comme matière grasse pour la cuisson ou les sauces.', 'unit' => $units['g']],
            ['name' => 'Oeufs', 'description' => 'Ingrédient essentiel pour la pâtisserie et de nombreux plats salés.', 'unit' => $units['u']],
            ['name' => 'Chocolat', 'description' => 'Ingrédient incontournable pour les desserts et les en-cas sucrés.', 'unit' => $units['g']],
            ['name' => 'Lait', 'description' => 'Produit laitier de base utilisé dans les boissons, desserts ou plats salés.', 'unit' => $units['ml']],
        ];

        IngredientFactory::createSequence($ingredients);

        $manager->flush();
    }

    #[\Override]
    public function getDependencies(): array
    {
        return [
            UnitFixtures::class,
        ];
    }
}
