<?php

namespace App\DataFixtures;

use App\Domain\Unit\UnitFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UnitFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $units = [
            ['label' => 'Kilogramme', 'shortLabel' => 'kg'],
            ['label' => 'Litre', 'shortLabel' => 'L'],
            ['label' => 'Gramme', 'shortLabel' => 'g'],
            ['label' => 'Millilitre', 'shortLabel' => 'ml'],
            ['label' => 'Pièce(s)', 'shortLabel' => 'pièce(s)'],
        ];

        UnitFactory::createSequence($units);

        $manager->flush();
    }
}
