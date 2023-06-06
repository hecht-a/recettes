<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Ingredient\Ingredient;
use App\Domain\IngredientRecipe\IngredientRecipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'attr' => [
                    'is' => 'select-choices'
                ],
            ])
            ->add('quantity', NumberType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => IngredientRecipe::class,
                'label' => false,
//                'row_attr' => [
//                    'is' => 'ingredient-field'
//                ]
            ]);
    }
}
