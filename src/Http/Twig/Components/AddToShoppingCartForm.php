<?php

namespace App\Http\Twig\Components;

use App\Domain\Auth\User;
use App\Domain\Recipe\Recipe;
use App\Http\Form\ShoppingListForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent]
final class AddToShoppingCartForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;
    use ValidatableComponentTrait;

    #[LiveProp]
    public Recipe $recipe;

    public function __construct(private readonly Security $security)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ShoppingListForm::class);
    }

    #[LiveAction]
    #[IsGranted('ROLE_USER')]
    public function add(): void
    {
    }
}
