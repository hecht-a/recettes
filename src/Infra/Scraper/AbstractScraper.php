<?php

namespace App\Infra\Scraper;

use App\Domain\Recipe\Recipe;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractScraper
{
    protected ?Recipe $recipe = null;

    protected const array SPECIAL_AMOUNTS = [
        '¼' => '0.25',
        '½' => '0.5',
    ];

    public function __construct(private readonly HttpClientInterface $httpClient)
    {
    }

    protected function getCrawler(string $scrapeUrl): Crawler
    {
        $response = $this->httpClient->request('GET', $scrapeUrl);

        return new Crawler($response->getContent());
    }

    protected function formatStrings(string $string, bool $toLower = true): string
    {
        if ($toLower) {
            $string = strtolower($string);
        }
        $string = str_replace('œ', 'oe', $string);
        $string = str_replace('Œ', 'oe', $string);

        return trim(ucfirst($string));
    }

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

    abstract protected function getTitle(Crawler $crawler): string;

    abstract protected function getDescription(Crawler $crawler): ?string;

    abstract protected function getIngredients(Crawler $crawler): array;

    abstract protected function getUtensils(Crawler $crawler): array;

    abstract protected function getAllergens(Crawler $crawler): array;

    abstract protected function getCategories(Crawler $crawler): array;

    abstract protected function getSteps(Crawler $crawler): array;
}
