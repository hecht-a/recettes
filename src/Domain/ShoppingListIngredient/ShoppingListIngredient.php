<?php

namespace App\Domain\ShoppingListIngredient;

use App\Domain\Ingredient\Ingredient;
use App\Domain\ShoppingList\ShoppingList;
use App\Infra\DateableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListIngredientRepository::class)]
class ShoppingListIngredient
{
    use DateableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ShoppingList::class, inversedBy: 'ingredients')]
    private ShoppingList $shoppingList;

    #[ORM\ManyToOne(targetEntity: Ingredient::class, inversedBy: 'shoppingListIngredients')]
    private Ingredient $ingredient;

    #[ORM\Column(type: Types::FLOAT, nullable: false)]
    private float $quantity;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    private bool $isChecked = false;

    public function __construct()
    {
        $this->initDateable();
        $this->isChecked = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShoppingList(): ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): void
    {
        $this->shoppingList = $shoppingList;
    }

    public function getIngredient(): Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): void
    {
        $this->ingredient = $ingredient;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function isChecked(): bool
    {
        return $this->isChecked;
    }

    public function setIsChecked(bool $isChecked): void
    {
        $this->isChecked = $isChecked;
    }
}
