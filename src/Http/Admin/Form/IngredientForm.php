<?php

namespace App\Http\Admin\Form;

use App\Domain\Ingredient\Ingredient;
use App\Domain\Unit\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class IngredientForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class)
            ->add('unit', EntityType::class, [
                'class' => Unit::class,
                'choice_label' => fn (Unit $unit) => sprintf('%s (%s)', $unit->getLabel(), $unit->getShortLabel()),
                'attr' => [
                    'is' => 'select-choices',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'allow_delete' => false,
                'download_uri' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);

        parent::configureOptions($resolver);
    }
}
