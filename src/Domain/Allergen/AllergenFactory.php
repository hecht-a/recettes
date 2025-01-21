<?php

namespace App\Domain\Allergen;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Allergen>
 */
final class AllergenFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Allergen::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(),
            'name' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
