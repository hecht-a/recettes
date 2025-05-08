<?php

namespace App\Domain\ShoppingList;

use App\Domain\Auth\User;
use App\Domain\ShoppingListIngredient\ShoppingListIngredient;
use App\Domain\ShoppingListRecipe\ShoppingListRecipe;
use App\Infra\DateableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
class ShoppingList
{
    use DateableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['remove', 'persist'], inversedBy: 'recipes')]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    private string $name;

    /** @var Collection<int, ShoppingListRecipe> */
    #[ORM\OneToMany(mappedBy: 'shoppingList', targetEntity: ShoppingListRecipe::class, cascade: ['persist', 'remove'])]
    private Collection $recipes;

    /** @var Collection<int, ShoppingListIngredient> */
    #[ORM\OneToMany(mappedBy: 'shoppingList', targetEntity: ShoppingListIngredient::class, cascade: ['persist', 'remove'])]
    private Collection $ingredients;

    public function __construct()
    {
        $this->initDateable();
        $this->recipes = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /** @return Collection<int, ShoppingListRecipe> */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipes(ShoppingListRecipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setShoppingList($this);
        }

        return $this;
    }

    public function removeRecipe(ShoppingListRecipe $recipe): self
    {
        if ($this->recipes->contains($recipe)) {
            $this->recipes->removeElement($recipe);
            $recipe->setShoppingList(null);
        }

        return $this;
    }

    /** @return Collection<int, ShoppingListIngredient> */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredients(ShoppingListIngredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setShoppingList($this);
        }

        return $this;
    }

    public function removeIngredient(ShoppingListIngredient $ingredient): self
    {
        if ($this->ingredients->contains($ingredient)) {
            $this->ingredients->removeElement($ingredient);
            $ingredient->setShoppingList(null);
        }

        return $this;
    }
}
