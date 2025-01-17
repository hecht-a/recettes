<?php

namespace App\Domain\Step;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Step>
 */
final class StepFactory extends PersistentProxyObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Step::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'description' => self::faker()->text(),
            'position' => self::faker()->randomNumber(),
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Step $step): void {})
        ;
    }
}
