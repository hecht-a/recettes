<?php

namespace App\Tests;

use App\Helper\PathHelper;
use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * @property EntityManagerInterface $em
 */
trait FixturesTrait
{
    /**
     * @param array<string> $fixtures
     *
     * @return array<string,object>
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function loadFixtures(array $fixtures): array
    {
        $fixturePath = $this->getFixturesPath();
        $files = array_map(fn ($fixture) => PathHelper::join($fixturePath, $fixture.'.yaml'), $fixtures);

        /** @var ContainerBagInterface $container */
        $container = $this->getContainer();

        /** @var LoaderInterface $loader */
        $loader = $container->get('fidry_alice_data_fixtures.loader.doctrine');

        return $loader->load($files, [], [], PurgeMode::createTruncateMode());
    }

    private function getFixturesPath()
    {
        return __DIR__.'/fixtures/';
    }
}
