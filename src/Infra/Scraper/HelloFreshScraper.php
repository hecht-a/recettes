<?php

namespace App\Infra\Scraper;

use App\Domain\Allergen\AllergenRepository;
use App\Domain\Category\CategoryRepository;
use App\Domain\Ingredient\IngredientRepository;
use App\Domain\IngredientRecipe\IngredientRecipeRepository;
use App\Domain\Recipe\Recipe;
use App\Domain\Step\StepRepository;
use App\Domain\Unit\UnitRepository;
use App\Domain\Utensil\UtensilRepository;
use App\Helper\Color\HexColorGenerator;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HelloFreshScraper extends AbstractScraper
{
    private ?Recipe $recipe = null;

    public function __construct(
        HttpClientInterface $httpClient,
        private readonly HexColorGenerator $hexColorGenerator,
        private readonly UtensilRepository $utensilRepository,
        private readonly AllergenRepository $allergenRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly StepRepository $stepRepository,
        private readonly UnitRepository $unitRepository,
        private readonly IngredientRepository $ingredientRepository,
        private readonly IngredientRecipeRepository $ingredientRecipeRepository,
    ) {
        parent::__construct($httpClient);
    }

    #[\Override]
    public function scrape(string $scrapeUrl): Recipe
    {
        $crawler = $this->getCrawler($scrapeUrl);

        $this->recipe = new Recipe();
        $this->recipe->setName($this->getTitle($crawler));
        $this->recipe->setDescription($this->getDescription($crawler));

        foreach ($this->getIngredients($crawler) as $ingredient) {
            $this->recipe->addIngredient($ingredient);
        }

        foreach ($this->getUtensils($crawler) as $utensil) {
            $this->recipe->addUtensil($utensil);
        }

        foreach ($this->getAllergens($crawler) as $allergen) {
            $this->recipe->addAllergen($allergen);
        }

        foreach ($this->getCategories($crawler) as $category) {
            $this->recipe->addCategory($category);
        }

        foreach ($this->getSteps($crawler) as $step) {
            $this->recipe->addStep($step);
        }

        return $this->recipe;
    }

    #[\Override]
    protected function getTitle(Crawler $crawler): string
    {
        $h1 = '';
        $h2 = '';

        try {
            $h1 = $crawler->filter('.ghBzOL')->text();
        } catch (\LogicException) {
        }

        try {
            $h2 = $crawler->filter('.cSYzey')->text();
        } catch (\LogicException) {
        }

        return trim("$h1 $h2");
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
            return $crawler->filter('.fVouJt')->each(function (Crawler $node, $i): object {
                $quantity = $node->filter('.ccrEYr')->text();
                [$amount, $unit] = explode(' ', $quantity);

                if (isset(self::SPECIAL_AMOUNTS[$amount])) {
                    $amount = self::SPECIAL_AMOUNTS[$amount];
                }

                $unit = strtolower($unit);
                $unit = $this->unitRepository->findOneOrCreate(['shortLabel' => $unit], [
                    'label' => $unit,
                    'shortLabel' => $unit,
                ], true);

                $ingredientName = $this->formatStrings($node->filter('.jELdJr')->text());

                $ingredient = $this->ingredientRepository->findOneOrCreate(['name' => $ingredientName], [
                    'name' => $ingredientName,
                    'description' => $ingredientName,
                    'unit' => $unit,
                ]);

                return $this->ingredientRecipeRepository->findOneOrCreate(['ingredient' => $ingredient, 'recipe' => $this->recipe], [
                    'ingredient' => $ingredient,
                    'quantity' => floatval($amount),
                    'recipe' => $this->recipe,
                ]);
            });
        } catch (\LogicException) {
            return [];
        }
    }

    #[\Override]
    protected function getUtensils(Crawler $crawler): array
    {
        try {
            return $crawler->filter('.BlmJr .hysTOH .dvaxob')->each(fn (Crawler $node, $i): object => $this->utensilRepository->findOneOrCreate(['name' => $node->text()], [
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
        try {
            return $crawler->filter('[data-test-id=recipe-description-allergen] .dvaxob')->each(fn (Crawler $node, $i): object => $this->allergenRepository->findOneOrCreate(['name' => $node->text()], [
                'name' => $node->text(),
                'description' => $node->text(),
            ]));
        } catch (\LogicException) {
            return [];
        }
    }

    #[\Override]
    protected function getCategories(Crawler $crawler): array
    {
        try {
            return $crawler->filter('[data-test-id=recipe-description-tag] .dvaxob')->each(function (Crawler $node, $i): object {
                $name = $this->formatStrings($node->text());

                return $this->categoryRepository->findOneOrCreate(['name' => $name], [
                    'name' => $name,
                    'description' => $name,
                    'color' => $this->hexColorGenerator->generate(),
                ]);
            });
        } catch (\LogicException) {
            return [];
        }
    }

    #[\Override]
    protected function getSteps(Crawler $crawler): array
    {
        try {
            return $crawler->filter('.gNOFyU')->each(fn (Crawler $node, $i): object => $this->stepRepository->findOneOrCreate(['description' => $node->html(), 'recipe' => $this->recipe], [
                'description' => $node->html(),
                'position' => $i + 1,
                'recipe' => $this->recipe,
            ]));
        } catch (\LogicException) {
            return [];
        }
    }
}
