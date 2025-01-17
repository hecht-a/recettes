<?php

namespace App\Domain\Unit;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Unit>
 */
final class UnitFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Unit::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'label' => self::faker()->text(255),
            'shortLabel' => self::faker()->randomLetter(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this;
    }
}
