<?php

namespace App\Http\Admin\Form\Field;

use App\Domain\Utensil\Utensil;
use App\Domain\Utensil\UtensilRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UtensilsType extends TextType implements DataTransformerInterface
{
    public function __construct(private readonly UtensilRepository $repository, private readonly UrlGeneratorInterface $urlGenerator)
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
                'data-remote' => $this->urlGenerator->generate('api_utensil_search'),
                'data-value' => 'name',
                'data-label' => 'name',
            ],
        ]);
        parent::configureOptions($resolver);
    }

    /**
     * @param string|Utensil[] $value
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
                fn (Utensil $utensil): string => "{$utensil->getName()}:{$utensil->getId()}",
                $value
            )
        );
    }

    /**
     * @param ?string $value
     *
     * @return Utensil[]
     */
    #[\Override]
    public function reverseTransform($value): array
    {
        if (empty($value)) {
            return [];
        }

        $ids = [];
        $utensils = explode(',', $value);
        foreach ($utensils as $utensil) {
            $parts = explode(':', trim($utensil));
            if (isset($parts[0]) && ('' !== $parts[0] && '0' !== $parts[0])) {
                $ids[$parts[0]] = $parts[1] ?? null;
            }
        }

        $utensils = $this->repository->findByNames(array_keys($ids));
        $utensilsByName = collect($utensils)->keyBy(fn ($utensil): string => $utensil->getName())->toArray();

        foreach (array_keys($ids) as $name) {
            if (!isset($utensilsByName[$name])) {
                $utensils[] = (new Utensil())
                    ->setName($name);
            }
        }

        return $utensils;
    }
}
