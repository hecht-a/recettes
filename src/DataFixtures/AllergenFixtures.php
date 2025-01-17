<?php

namespace App\DataFixtures;

use App\Domain\Allergen\AllergenFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AllergenFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $allergens = [
            ['name' => 'Gluten', 'description' => 'Protéine présente dans le blé, l’orge et le seigle.'],
            ['name' => 'Lactose', 'description' => 'Sucre naturel présent dans le lait et les produits laitiers.'],
            ['name' => 'Arachides', 'description' => 'Noix très allergène, souvent utilisée dans des produits transformés.'],
            ['name' => 'Oeufs', 'description' => 'Ingrédient commun dans de nombreuses recettes, allergène fréquent.'],
            ['name' => 'Soja', 'description' => 'Légumineuse utilisée dans de nombreux produits, y compris les sauces.'],
            ['name' => 'Fruits à coque', 'description' => 'Noix comme les amandes, noisettes et noix de cajou.'],
            ['name' => 'Poissons', 'description' => 'Protéines présentes dans les poissons, allergène commun des produits de la mer.'],
            ['name' => 'Crustacés', 'description' => 'Inclut les crevettes, crabes, langoustes, etc.'],
            ['name' => 'Mollusques', 'description' => 'Inclut les moules, huîtres, calamars et escargots.'],
            ['name' => 'Céleri', 'description' => 'Légume souvent utilisé dans les soupes et les plats préparés.'],
            ['name' => 'Moutarde', 'description' => 'Condiment courant, présent dans de nombreux assaisonnements.'],
            ['name' => 'Sésame', 'description' => 'Graines utilisées dans les pains, sauces et plats orientaux.'],
            ['name' => 'Lupin', 'description' => 'Légumineuse souvent utilisée dans la farine et certains produits de boulangerie.'],
            ['name' => 'Sulfites', 'description' => 'Conservateurs présents dans les vins, fruits secs et aliments transformés.'],
            ['name' => 'Lait', 'description' => 'Présent dans de nombreux produits laitiers comme le fromage et le beurre.'],
        ];

        AllergenFactory::createSequence($allergens);

        $manager->flush();
    }
}
