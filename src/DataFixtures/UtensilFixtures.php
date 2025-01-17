<?php

namespace App\DataFixtures;

use App\Domain\Utensil\UtensilFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UtensilFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $utensils = [
            ['name' => 'Couteau de chef', 'description' => 'Un couteau polyvalent pour couper, trancher et hacher.'],
            ['name' => 'Spatule', 'description' => 'Outil utilisé pour retourner ou mélanger les aliments.'],
            ['name' => 'Fouet', 'description' => 'Ustensile pour battre les œufs, mélanger ou émulsionner.'],
            ['name' => 'Cuillère en bois', 'description' => 'Parfaite pour mélanger les préparations sans rayer les casseroles.'],
            ['name' => 'Eplucheur', 'description' => 'Utilisé pour enlever la peau des fruits et légumes.'],
            ['name' => 'Passoire', 'description' => 'Ustensile pour égoutter les pâtes ou rincer les légumes.'],
            ['name' => 'Mixeur', 'description' => 'Appareil électrique pour mixer des préparations.'],
            ['name' => 'Poêle', 'description' => 'Ustensile de cuisson pour saisir et griller les aliments.'],
            ['name' => 'Casserole', 'description' => 'Récipient pour cuire les aliments à feu doux ou bouillir de l’eau.'],
            ['name' => 'Planche à découper', 'description' => 'Surface pour couper les aliments en toute sécurité.'],
            ['name' => 'Louche', 'description' => 'Idéale pour servir les soupes et sauces.'],
            ['name' => 'Râpe', 'description' => 'Ustensile pour râper le fromage, les légumes ou le zeste.'],
            ['name' => 'Tamis', 'description' => 'Utilisé pour tamiser la farine ou saupoudrer du sucre glace.'],
            ['name' => 'Thermomètre de cuisine', 'description' => 'Permet de mesurer la température des préparations.'],
            ['name' => 'Moules à gâteaux', 'description' => 'Ustensiles pour cuire des gâteaux et pâtisseries.'],
        ];

        UtensilFactory::createSequence($utensils);
        $manager->flush();
    }
}
