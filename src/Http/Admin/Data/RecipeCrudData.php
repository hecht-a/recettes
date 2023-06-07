<?php

namespace App\Http\Admin\Data;

use App\Domain\Allergen\Allergen;
use App\Domain\Auth\User;
use App\Domain\Category\Category;
use App\Domain\Ingredient\Ingredient;
use App\Domain\IngredientRecipe\IngredientRecipe;
use App\Domain\Recipe\Recipe;
use App\Domain\Step\Step;
use App\Domain\Utensil\Utensil;
use App\Http\Admin\Form\RecipeForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
class RecipeCrudData implements CrudDataInterface
{
    public ?User $user;

    #[Assert\NotBlank]
    public ?string $name = null;

    #[Assert\NotBlank]
    public ?string $description = null;

    public \DateTimeInterface $createdAt;

    #[Assert\GreaterThan(0)]
    public ?int $preparationTime = 0;

    #[Assert\GreaterThan(0)]
    public ?int $cookingTime = 0;

    /** @var Category[] */
    public array $categories = [];

    /** @var Allergen[] */
    public array $allergens = [];

    /** @var Utensil[] */
    public array $utensils = [];

    /** @var IngredientRecipe[] */
    public array $ingredients = [];

    /** @var Step[] */
    public array $steps = [];

    #[Vich\UploadableField(mapping: 'recipe', fileNameProperty: 'imageName')]
    public ?File $image = null;

    public ?string $imageName = null;

    private EntityManagerInterface $em;

    public function __construct(private readonly Recipe $entity)
    {
        $this->user = $entity->getUser();
        $this->name = $entity->getName();
        $this->description = $entity->getDescription();
        $this->createdAt = $entity->getCreatedAt();
        $this->preparationTime = $entity->getPreparationTime();
        $this->cookingTime = $entity->getCookingTime();
        $this->categories = $entity->getCategories()->toArray();
        $this->allergens = $entity->getAllergens()->toArray();
        $this->utensils = $entity->getUtensils()->toArray();
        $this->ingredients = $entity->getIngredients()->toArray();
        $this->steps = $entity->getSteps()->toArray();
        $this->image = $this->entity->getImageFile();
    }

    public function hydrate(): void
    {
        $this->entity->setUser($this->user);
        $this->entity->setName($this->name);
        $this->entity->setDescription($this->description);
        $this->entity->setCreatedAt($this->createdAt);
        $this->entity->setPreparationTime($this->preparationTime);
        $this->entity->setCookingTime($this->cookingTime);
        $this->entity->setImageFile($this->image);

        //TODO: check if the next code can be refactored and simplified to remove a foreach
        /** @var Category[] $oldCategories */
        $oldCategories = $this->entity->getCategories()->toArray();
        $newCategories = $this->categories;

        foreach ($oldCategories as $oldCategory) {
            $this->entity->removeCategory($oldCategory);
        }
        foreach ($newCategories as $newCategory) {
            $this->entity->addCategory($newCategory);
        }

        /** @var Allergen[] $oldAllergens */
        $oldAllergens = $this->entity->getAllergens()->toArray();
        $newAllergens = $this->allergens;

        foreach ($oldAllergens as $oldAllergen) {
            $this->entity->removeAllergen($oldAllergen);
        }
        foreach ($newAllergens as $newAllergen) {
            $this->entity->addAllergen($newAllergen);
        }

        /** @var Utensil[] $oldUtensils */
        $oldUtensils = $this->entity->getUtensils()->toArray();
        $newUtensils = $this->utensils;

        foreach ($oldUtensils as $oldUtensil) {
            $this->entity->removeUtensil($oldUtensil);
        }
        foreach ($newUtensils as $newUtensil) {
            $this->entity->addUtensil($newUtensil);
        }

        /** @var IngredientRecipe[] $oldIngredients */
        $oldIngredients = $this->entity->getIngredients()->toArray();
        $newIngredients = $this->ingredients;

        foreach ($oldIngredients as $oldIngredient) {
            if(!in_array($oldIngredient, $newIngredients)) {
                $this->em->remove($oldIngredient);
            }
        }

        foreach ($newIngredients as $newIngredient) {
            $this->entity->addIngredient($newIngredient->setRecipe($this->entity));
        }

        /** @var Step[] $oldSteps */
        $oldSteps = $this->entity->getSteps()->toArray();
        /** @var Step[] $newSteps */
        $newSteps = [...$this->steps];

        foreach ($oldSteps as $oldStep) {
            if(!in_array($oldStep, $newSteps)) {
                $this->em->remove($oldStep);
            }
        }

        // TODO: fix step order when editing the order
        foreach ($newSteps as $key => $newStep) {
            $this->entity->addStep($newStep->setRecipe($this->entity)->setPosition($key + 1));
        }
    }

    public function getEntity(): Recipe
    {
        return $this->entity;
    }

    public function getFormClass(): string
    {
        return RecipeForm::class;
    }

    public function setEntityManager(EntityManagerInterface $em): self
    {
        $this->em = $em;

        return $this;
    }
}
