<?php

namespace App\Domain\Ingredient;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Ingredient>
 */
final class IngredientFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Ingredient::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'description' => self::faker()->text(),
            'name' => self::faker()->text(255),
            'updatedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
        ;
    }
}
