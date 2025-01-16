<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Ingredient\Ingredient;
use App\Domain\IngredientRecipe\IngredientRecipe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IngredientRecipeType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'attr' => [
                    'is' => 'select-choices',
                ],
            ])
            ->add('quantity', NumberType::class)
            ->add('delete', ButtonType::class, [
                'row_attr' => [
                    'class' => 'delete_row',
                ],
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => IngredientRecipe::class,
                'label' => false,
            ]);
    }
}
