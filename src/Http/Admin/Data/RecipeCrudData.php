<?php

namespace App\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Domain\Category\Category;
use App\Domain\Recipe\Recipe;
use App\Domain\Step\Step;
use App\Http\Admin\Form\RecipeForm;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
    }

    public function hydrate(): void
    {
        $this->entity->setUser($this->user);
        $this->entity->setName($this->name);
        $this->entity->setDescription($this->description);
        $this->entity->setCreatedAt($this->createdAt);
        $this->entity->setPreparationTime($this->preparationTime);
        $this->entity->setCookingTime($this->cookingTime);

        $categories = $this->entity->getCategories()->filter(fn(Category $category) => in_array($category, $this->categories));
        foreach ($this->entity->getCategories() as $category) {
            $this->entity->removeCategory($category);
        }


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
