<?php

namespace App\Domain\Category;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class CategoryFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Category::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'color' => self::faker()->hexColor(),
            'description' => self::faker()->text(),
            'name' => self::faker()->text(255),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
        ;
    }
}
