<?php

namespace App\Http\Twig\Components\Recipe;

use App\Domain\Recipe\Recipe;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Title
{
    use DefaultActionTrait;

    public ?string $id = null;

    #[LiveProp]
    public Recipe $recipe;

    #[LiveProp]
    public bool $isModalOpened = false;

    #[LiveAction]
    #[LiveListener('modal:toggle')]
    public function toggleModal(): void
    {
        $this->isModalOpened = !$this->isModalOpened;
    }
}
