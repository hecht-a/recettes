<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Category\Category;
use App\Domain\Category\CategoryRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoriesType extends TextType implements DataTransformerInterface
{
    public function __construct(private readonly CategoryRepository $repository, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addViewTransformer($this);
        parent::buildForm($builder, $options);
    }

    #[\Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'attr' => [
                'is' => 'input-choices',
                'data-remote' => $this->urlGenerator->generate('api_category_search'),
                'data-value' => 'name',
                'data-label' => 'name',
            ],
        ]);
        parent::configureOptions($resolver);
    }

    /**
     * @param string|Category[] $value
     */
    #[\Override]
    public function transform($value): ?string
    {
        if (!is_array($value)) {
            return null;
        }

        return implode(
            ',',
            array_map(
                fn (Category $category): string => "{$category->getName()}:{$category->getId()}",
                $value
            )
        );
    }

    /**
     * @param ?string $value
     *
     * @return Category[]
     */
    #[\Override]
    public function reverseTransform($value): array
    {
        if (empty($value)) {
            return [];
        }

        $ids = [];
        $categories = explode(',', $value);
        foreach ($categories as $category) {
            $parts = explode(':', trim($category));
            if (isset($parts[0]) && ('' !== $parts[0] && '0' !== $parts[0])) {
                $ids[$parts[0]] = $parts[1] ?? null;
            }
        }

        $categories = $this->repository->findByNames(array_keys($ids));
        $categoriesByName = collect($categories)->keyBy(fn ($category): ?string => $category->getName())->toArray();

        foreach (array_keys($ids) as $name) {
            if (!isset($categoriesByName[$name])) {
                $categories[] = (new Category())
                    ->setName($name);
            }
        }

        return $categories;
    }
}
