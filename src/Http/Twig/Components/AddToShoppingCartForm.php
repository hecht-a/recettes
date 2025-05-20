<?php

namespace App\Http\Twig\Components;

use App\Domain\Auth\User;
use App\Domain\Recipe\Recipe;
use App\Domain\ShoppingList\ShoppingList;
use App\Domain\ShoppingListRecipe\ShoppingListRecipe;
use App\Http\Form\ShoppingListForm;
use Doctrine\ORM\EntityManagerInterface;
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
    use ComponentToolsTrait;

    #[LiveProp]
    public Recipe $recipe;

    public function __construct(private readonly Security $security, private readonly EntityManagerInterface $entityManager)
    {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ShoppingListForm::class, new ShoppingList());
    }

    #[LiveAction]
    #[IsGranted('ROLE_USER')]
    public function add(): void
    {
        $this->submitForm();

        $form = $this->getForm();

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var ShoppingList $shoppingList */
            $shoppingList = $this->form->getData();

            /** @var User $user */
            $user = $this->security->getUser();
            $filter = $user->getShoppingLists()->filter(fn (ShoppingList $list) => $list->getName() === $shoppingList->getName());

            if ($filter->isEmpty()) {
                $this->entityManager->persist($shoppingList);
            } else {
                $shoppingList = $filter->first();
            }

            $shoppingListRecipe = new ShoppingListRecipe();
            $shoppingListRecipe->setRecipe($this->recipe);

            $this->entityManager->persist($shoppingListRecipe);

            $shoppingList->addRecipe($shoppingListRecipe);
            $shoppingList->setUser($user);

            $this->entityManager->flush();

            $this->emit('alert:message', [
                'type' => 'success',
                'message' => 'Recette ajoutée à la liste de courses.'
            ]);

            $this->emit('modal:toggle');
        }
    }
}
