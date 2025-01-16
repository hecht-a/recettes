<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Step\Step;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepsType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('move', ButtonType::class, [
                'row_attr' => [
                    'class' => 'move_row',
                ],
            ])
            ->add('description', TextareaType::class)
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
                'data_class' => Step::class,
                'label' => false,
            ]);
    }
}
