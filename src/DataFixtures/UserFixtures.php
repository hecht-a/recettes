<?php

namespace App\DataFixtures;

use App\Domain\Auth\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'admin@recettes.com',
            'name' => 'admin',
            'password' => '0000',
            'roles' => ['ROLE_ADMIN', 'ROLE_USER'],
        ]);

        UserFactory::createOne([
            'email' => 'user@recettes.com',
            'name' => 'user',
            'password' => '0000',
            'roles' => ['ROLE_USER'],
        ]);

        UserFactory::createMany(5);

        $manager->flush();
    }
}
