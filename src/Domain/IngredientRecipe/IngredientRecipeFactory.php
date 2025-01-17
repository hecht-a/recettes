<?php

namespace App\Domain\IngredientRecipe;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<IngredientRecipe>
 */
final class IngredientRecipeFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return IngredientRecipe::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'quantity' => self::faker()->randomFloat(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
        ;
    }
}
