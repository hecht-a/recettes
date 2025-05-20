<?php

namespace App\Http\Form;

use App\Domain\ShoppingList\ShoppingList;
use App\Domain\ShoppingList\ShoppingListDTO;
use App\Domain\ShoppingList\ShoppingListRepository;
use App\Http\Form\Field\ShoppingListNameAutocompleteField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class ShoppingListForm extends AbstractType
{
    public function __construct(private readonly ShoppingListRepository $shoppingListRepository, private readonly Security $security)
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $shoppingLists = $this->shoppingListRepository->findAll();

        $builder
            ->add('name', TextType::class, [
                'label' => 'Choisir la liste',
                'autocomplete' => true,
                'tom_select_options' => [
                    'create' => true,
                    'createOnBlur' => true,
                    'maxItems' => 1,
                    'valueField' => 'id',
                    'labelField' => 'title',
                    'searchField' => 'title',
                    'options' => array_map(fn(ShoppingList $list) => [
                        'id' => $list->getName(),
                        'title' => $list->getName(),
                    ], $this->shoppingListRepository->findBy(['user' => $this->security->getUser()]))
                ],
                'constraints' => [
                    new NotNull(message: 'Ce champ doit être rempli')
                ]
            ]);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ShoppingList::class,
        ]);
    }
}
