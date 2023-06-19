<?php

namespace App\Domain\Recipe;

use App\Domain\Allergen\Allergen;
use App\Domain\Auth\User;
use App\Domain\Category\Category;
use App\Domain\IngredientRecipe\IngredientRecipe;
use App\Domain\Step\Step;
use App\Domain\Utensil\Utensil;
use App\Infra\Interfaces\IdentifiableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[Vich\Uploadable]
class Recipe implements IdentifiableInterface
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Assert\NotBlank]
    private \DateTimeImmutable $updatedAt;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $preparationTime = 0;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $cookingTime = 0;

    /** @var ArrayCollection<int, IngredientRecipe> */
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: IngredientRecipe::class, cascade: ['persist', 'remove'])]
    private Collection $ingredients;

    /** @var ArrayCollection<int, Category> */
    #[Orm\ManyToMany(targetEntity: Category::class, mappedBy: 'recipes')]
    private Collection $categories;

    /** @var ArrayCollection<int, Utensil> */
    #[ORM\ManyToMany(targetEntity: Utensil::class, mappedBy: 'recipes')]
    private Collection $utensils;

    /** @var ArrayCollection<int, Allergen> */
    #[ORM\ManyToMany(targetEntity: Allergen::class, mappedBy: 'recipes')]
    private Collection $allergens;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'recipes')]
    private ?User $user;

    #[Vich\UploadableField(mapping: 'recipe', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    /** @var ArrayCollection<int, Step> */
    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: Step::class, cascade: ['persist', 'remove'])]
    private Collection $steps;

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private string $slug;

    /** @var ArrayCollection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteRecipes')]
    #[ORM\JoinTable(name: 'favorite_recipe')]
    private Collection $usersFavorite;

    #[ORM\Column(type: Types::INTEGER, nullable: false)]
    private int $amountPersons;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->ingredients = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->utensils = new ArrayCollection();
        $this->allergens = new ArrayCollection();
        $this->steps = new ArrayCollection();
        $this->usersFavorite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(int $preparationTime): self
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    public function getCookingTime(): ?int
    {
        return $this->cookingTime;
    }

    public function setCookingTime(int $cookingTime): self
    {
        $this->cookingTime = $cookingTime;

        return $this;
    }

    /**
     * @return ArrayCollection<int, IngredientRecipe>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(IngredientRecipe $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(IngredientRecipe $ingredient): self
    {
        if ($this->ingredients->contains($ingredient)) {
            $this->ingredients->removeElement($ingredient);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addRecipe($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeRecipe($this);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Utensil>
     */
    public function getUtensils(): Collection
    {
        return $this->utensils;
    }

    public function addUtensil(Utensil $utensil): self
    {
        if (!$this->utensils->contains($utensil)) {
            $this->utensils[] = $utensil;
            $utensil->addRecipe($this);
        }

        return $this;
    }

    public function removeUtensil(Utensil $utensil): self
    {
        if ($this->utensils->contains($utensil)) {
            $this->utensils->removeElement($utensil);
            $utensil->removeRecipe($this);
        }

        return $this;
    }

    /**
     * @return ArrayCollection<int, Allergen>
     */
    public function getAllergens(): Collection
    {
        return $this->allergens;
    }

    public function addAllergen(Allergen $allergen): self
    {
        if (!$this->allergens->contains($allergen)) {
            $this->allergens[] = $allergen;
            $allergen->addRecipe($this);
        }

        return $this;
    }

    public function removeAllergen(Allergen $allergen): self
    {
        if ($this->allergens->contains($allergen)) {
            $this->allergens->removeElement($allergen);
            $allergen->removeRecipe($this);
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile !== null) {
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

    /**
     * @return ArrayCollection<int, Step>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Step $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
        }

        return $this;
    }

    public function removeStep(Step $step): self
    {
        if ($this->steps->contains($step)) {
            $this->steps->removeElement($step);
        }

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return ArrayCollection<int, User>
     */
    public function getUsersFavorite(): Collection
    {
        return $this->usersFavorite;
    }

    public function addUserFavorite(User $usersFavorite): self
    {
        if (!$this->usersFavorite->contains($usersFavorite)) {
            $this->usersFavorite[] = $usersFavorite;
            $usersFavorite->addFavoriteRecipe($this);
        }

        return $this;
    }

    public function removeUserFavorite(User $usersFavorite): self
    {
        if ($this->usersFavorite->contains($usersFavorite)) {
            $this->usersFavorite->removeElement($usersFavorite);
            $usersFavorite->removeFavoriteRecipe($this);
        }

        return $this;
    }

    public function getAmountPersons(): int
    {
        return $this->amountPersons;
    }

    public function setAmountPersons(int $amountPersons): Recipe
    {
        $this->amountPersons = $amountPersons;

        return $this;
    }
}
