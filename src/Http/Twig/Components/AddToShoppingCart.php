<?php

namespace App\Http\Twig\Components;

use App\Domain\Recipe\Recipe;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class AddToShoppingCart
{
    use DefaultActionTrait;

    #[LiveProp]
    public Recipe $recipe;

    #[LiveAction]
    public function add(): void
    {
        // TODO
    }
}
