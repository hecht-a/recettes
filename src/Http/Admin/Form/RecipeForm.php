<?php

namespace App\Http\Admin\Form;

use App\Domain\Recipe\Recipe;
use App\Http\Admin\Form\Field\AllergensType;
use App\Http\Admin\Form\Field\CategoriesType;
use App\Http\Admin\Form\Field\IngredientRecipeType;
use App\Http\Admin\Form\Field\StepsType;
use App\Http\Admin\Form\Field\UtensilsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('preparationTime', IntegerType::class)
            ->add('cookingTime', IntegerType::class)
            ->add('categories', CategoriesType::class, [
                'required' => false,
            ])
            ->add('allergens', AllergensType::class, [
                'required' => false,
            ])
            ->add('utensils', UtensilsType::class, [
                'required' => false,
            ])
            ->add('ingredients', CollectionType::class, [
                'entry_type' => IngredientRecipeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'row_attr' => [
                    'label' => false,
                    'class' => 'ingredients',
                    'is' => 'ingredients-wrapper'
                ],
            ])
            ->add('steps', CollectionType::class, [
                'entry_type' => StepsType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'row_attr' => [
                    'label' => false,
                    'class' => 'steps',
                    'is' => 'steps-wrapper'
                ],
            ])
            ->add('image', VichImageType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);

        parent::configureOptions($resolver);
    }
}
