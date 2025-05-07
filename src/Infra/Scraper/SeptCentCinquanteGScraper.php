<?php

namespace App\Infra\Scraper;

use App\Domain\Admin\SeptCentCinquanteG\SCCGIngredientDto;
use App\Domain\Ingredient\IngredientRepository;
use App\Domain\IngredientRecipe\IngredientRecipeRepository;
use App\Domain\Step\StepRepository;
use App\Domain\Unit\UnitRepository;
use App\Domain\Utensil\UtensilRepository;
use AutoMapper\AutoMapperInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SeptCentCinquanteGScraper extends AbstractScraper
{
    public function __construct(
        HttpClientInterface $httpClient,
        private readonly StepRepository $stepRepository,
        private readonly UnitRepository $unitRepository,
        private readonly IngredientRepository $ingredientRepository,
        private readonly IngredientRecipeRepository $ingredientRecipeRepository,
        private readonly AutoMapperInterface $autoMapper, private readonly UtensilRepository $utensilRepository,
    ) {
        parent::__construct($httpClient);
    }

    #[\Override]
    protected function getTitle(Crawler $crawler): string
    {
        try {
            return $crawler->filter('.u-title-page')->text();
        } catch (\LogicException) {
            return '';
        }
    }

    #[\Override]
    protected function getDescription(Crawler $crawler): ?string
    {
        return '';
    }

    #[\Override]
    protected function getIngredients(Crawler $crawler): array
    {
        $rawIngredients = json_decode($crawler->filter('.js-ingredient-variator-data')->text(), true);

        /** @var SCCGIngredientDto $rawIngredients */
        $rawIngredients = $this->autoMapper->map($rawIngredients, SCCGIngredientDto::class);

        $ingredients = [];

        foreach ($rawIngredients->recipeRawgredients as $recipeRawgredient) {
            foreach ($recipeRawgredient->ingredients as $rawIngredient) {
                $ingredientUnit = $rawIngredient->unit;
                if ($ingredientUnit) {
                    $unit = $this->unitRepository->findOneOrCreate(['shortLabel' => $ingredientUnit->abbr ?? $ingredientUnit->singular], [
                        'label' => $ingredientUnit->singular,
                        'shortLabel' => $ingredientUnit->abbr ?? $ingredientUnit->singular,
                    ], true);
                }

                $ingredientName = $this->formatStrings($rawIngredient->singular);

                $ingredient = $this->ingredientRepository->findOneOrCreate(['name' => $ingredientName], [
                    'name' => $ingredientName,
                    'description' => $ingredientName,
                    'unit' => $unit ?? null,
                ]);

                $ingredients[] = $this->ingredientRecipeRepository->findOneOrCreate(['ingredient' => $ingredient, 'recipe' => $this->recipe], [
                    'ingredient' => $ingredient,
                    'quantity' => floatval($rawIngredient->quantity),
                    'recipe' => $this->recipe,
                ]);
            }
        }

        return $ingredients;
    }

    #[\Override]
    protected function getUtensils(Crawler $crawler): array
    {
        try {
            return $crawler->filter('.recipe-equipments-item .recipe-equipments-item-label')->each(fn (Crawler $node, $i): object => $this->utensilRepository->findOneOrCreate(['name' => $node->text()], [
                'name' => $node->text(),
                'description' => $node->text(),
            ]));
        } catch (\LogicException) {
            return [];
        }
    }

    #[\Override]
    protected function getAllergens(Crawler $crawler): array
    {
        return [];
    }

    #[\Override]
    protected function getCategories(Crawler $crawler): array
    {
        return [];
    }

    #[\Override]
    protected function getSteps(Crawler $crawler): array
    {
        try {
            return $crawler->filter('.recipe-steps-item .recipe-steps-text')->each(fn (Crawler $node, $i): object => $this->stepRepository->findOneOrCreate(['description' => $node->html(), 'recipe' => $this->recipe], [
                'description' => $this->formatStrings($node->html(), false),
                'position' => $i + 1,
                'recipe' => $this->recipe,
            ]));
        } catch (\LogicException) {
            return [];
        }
    }
}
