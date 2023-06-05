<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Allergen\Allergen;
use App\Domain\Allergen\AllergenRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AllergensType extends TextType implements DataTransformerInterface
{
    public function __construct(private readonly AllergenRepository $repository, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'attr' => [
                'is' => 'input-choices',
                'data-remote' => $this->urlGenerator->generate('api_allergen_search'),
                'data-value' => 'name',
                'data-label' => 'name',
            ],
        ]);
        parent::configureOptions($resolver);
    }

    /**
     * @param string|Allergen[] $value
     */
    public function transform($value): ?string
    {
        if (!is_array($value)) {
            return null;
        }

        return implode(
            ',',
            array_map(
                fn (Allergen $allergen): string => "{$allergen->getName()}:{$allergen->getId()}",
                $value
            )
        );
    }

    /**
     * @param ?string $value
     * @return Allergen[]
     */
    public function reverseTransform($value): array
    {
        if (empty($value)) {
            return [];
        }

        $ids = [];
        $allergens = explode(',', $value);
        foreach ($allergens as $allergen) {
            $parts = explode(':', trim($allergen));
            if (!empty($parts[0])) {
                $ids[$parts[0]] = $parts[1] ?? null;
            }
        }

        $allergens = $this->repository->findByNames(array_keys($ids));
        $allergensByName = collect($allergens)->keyBy(fn ($allergen) => $allergen->getName())->toArray();

        foreach ($ids as $name => $version) {
            if (!isset($allergensByName[$name])) {
                $allergens[] = (new Allergen())
                    ->setName($name);
            }
        }

        return $allergens;
    }
}
