<?php

namespace App\Domain\Ingredient;

use App\Domain\IngredientRecipe\IngredientRecipe;
use App\Domain\ShoppingListIngredient\ShoppingListIngredient;
use App\Domain\Unit\Unit;
use App\Infra\Interfaces\IdentifiableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[UniqueEntity(fields: ['name'])]
#[Vich\Uploadable]
class Ingredient implements IdentifiableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: false)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank]
    private string $description;

    /** @var Collection<int, IngredientRecipe> */
    #[ORM\OneToMany(mappedBy: 'ingredient', targetEntity: IngredientRecipe::class)]
    private Collection $recipes;

    #[ORM\ManyToOne(targetEntity: Unit::class, inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Unit $unit = null;

    #[Vich\UploadableField(mapping: 'ingredient', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    public string $slug;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, ShoppingListIngredient> */
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: ShoppingListIngredient::class)]
    private Collection $shoppingListIngredients;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->shoppingListIngredients = new ArrayCollection();
    }

    #[\Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, IngredientRecipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(IngredientRecipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
        }

        return $this;
    }

    public function removeRecipe(IngredientRecipe $recipe): self
    {
        if ($this->recipes->contains($recipe)) {
            $this->recipes->removeElement($recipe);
        }

        return $this;
    }

    public function getUnit(): ?Unit
    {
        return $this->unit;
    }

    public function setUnit(?Unit $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile instanceof File) {
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): Ingredient
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): Ingredient
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /** @return Collection<int, ShoppingListIngredient> */
    public function getShoppingListIngredient(): Collection
    {
        return $this->shoppingListIngredients;
    }

    public function addShoppingListIngredient(ShoppingListIngredient $shoppingListIngredient): self
    {
        if (!$this->shoppingListIngredients->contains($shoppingListIngredient)) {
            $this->shoppingListIngredients[] = $shoppingListIngredient;
            $shoppingListIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeShoppingListIngredient(ShoppingListIngredient $shoppingListIngredient): self
    {
        if ($this->shoppingListIngredients->contains($shoppingListIngredient)) {
            $this->shoppingListIngredients->removeElement($shoppingListIngredient);
            $shoppingListIngredient->setIngredient(null);
        }

        return $this;
    }
}
