<?php

namespace App\Domain\Recipe;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Recipe>
 */
final class RecipeFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Recipe::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'amountPersons' => self::faker()->randomNumber(),
            'cookingTime' => self::faker()->randomNumber(),
            'createdAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'description' => self::faker()->text(),
            'name' => self::faker()->text(50),
            'preparationTime' => self::faker()->randomNumber(),
            'slug' => self::faker()->text(128),
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
