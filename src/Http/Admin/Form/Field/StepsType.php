<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Ingredient\Ingredient;
use App\Domain\IngredientRecipe\IngredientRecipe;
use App\Domain\Step\Step;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('move', ButtonType::class, [
                'row_attr' => [
                    'class' => 'move_row'
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('delete', ButtonType::class, [
                'row_attr' => [
                    'class' => 'delete_row'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => Step::class,
                'label' => false,
            ]);
    }
}
