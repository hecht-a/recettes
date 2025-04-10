<?php

namespace App\Infra\Scraper;

use App\Domain\Ingredient\IngredientRepository;
use App\Domain\IngredientRecipe\IngredientRecipeRepository;
use App\Domain\Step\StepRepository;
use App\Domain\Unit\UnitRepository;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MarmitonScraper extends AbstractScraper
{
    public function __construct(
        HttpClientInterface $httpClient,
        private readonly StepRepository $stepRepository,
        private readonly UnitRepository $unitRepository,
        private readonly IngredientRepository $ingredientRepository,
        private readonly IngredientRecipeRepository $ingredientRecipeRepository,
    ) {
        parent::__construct($httpClient);
    }

    #[\Override]
    protected function getTitle(Crawler $crawler): string
    {
        try {
            return $crawler->filter('.main-title h1')->text();
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
        try {
            return $crawler->filter('.card-ingredient')->each(function (Crawler $node, $i): object {
                $quantity = $node->filter('.card-ingredient-quantity')->text();
                if ('' !== $quantity) {
                    $exploded = explode(' ', $quantity);
                    $amount = $exploded[0];
                    $unit = $exploded[1] ?? '';

                    $unit = strtolower($unit);
                    $unit = $this->unitRepository->findOneOrCreate(['shortLabel' => $unit], [
                        'label' => $unit,
                        'shortLabel' => $unit,
                    ], true);
                }

                $ingredientName = $this->formatStrings(
                    $node->filter('.ingredient-name')->text() . ' ' . $node->filter('.ingredient-complement')->text()
                );

                $ingredient = $this->ingredientRepository->findOneOrCreate(['name' => $ingredientName], [
                    'name' => $ingredientName,
                    'description' => $ingredientName,
                    'unit' => $unit ?? null,
                ]);

                return $this->ingredientRecipeRepository->findOneOrCreate(['ingredient' => $ingredient, 'recipe' => $this->recipe], [
                    'ingredient' => $ingredient,
                    'quantity' => floatval($amount ?? 0),
                    'recipe' => $this->recipe,
                ]);
            });
        } catch (\LogicException $e) {
            return [];
        }
    }

    #[\Override]
    protected function getUtensils(Crawler $crawler): array
    {
        return [];
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
            return $crawler->filter('.recipe-step-list__container p')->each(fn (Crawler $node, $i): object => $this->stepRepository->findOneOrCreate(['description' => $node->html(), 'recipe' => $this->recipe], [
                'description' => $this->formatStrings($node->html()),
                'position' => $i + 1,
                'recipe' => $this->recipe,
            ]));
        } catch (\LogicException) {
            return [];
        }
    }
}
