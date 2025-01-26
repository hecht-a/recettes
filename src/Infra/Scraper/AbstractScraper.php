<?php

namespace App\Infra\Scraper;

use App\Domain\Recipe\Recipe;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractScraper
{
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

    protected function formatStrings(string $string): string
    {
        $string = strtolower($string);
        $string = str_replace('œ', 'oe', $string);
        $string = str_replace('Œ', 'oe', $string);

        return ucfirst($string);
    }

    abstract public function scrape(string $scrapeUrl): Recipe;

    abstract protected function getTitle(Crawler $crawler): string;

    abstract protected function getDescription(Crawler $crawler): ?string;

    abstract protected function getIngredients(Crawler $crawler): array;

    abstract protected function getUtensils(Crawler $crawler): array;

    abstract protected function getAllergens(Crawler $crawler): array;

    abstract protected function getCategories(Crawler $crawler): array;

    abstract protected function getSteps(Crawler $crawler): array;
}
