<?php

namespace App\Infra\Scraper;

use App\Domain\Recipe\Recipe;

readonly class ScraperProvider
{
    public function __construct(
        private HelloFreshScraper $helloFreshScraper,
        private MarmitonScraper $marmitonScraper,
        private SeptCentCinquanteGScraper $septCentCinquanteGScraper,
    ) {
    }

    public function scrape(string $url): Recipe
    {
        $scraper = match (true) {
            str_contains($url, 'hellofresh.') => $this->helloFreshScraper,
            str_contains($url, 'marmiton.org') => $this->marmitonScraper,
            str_contains($url, '750g.com') => $this->septCentCinquanteGScraper,
            default => throw new \InvalidArgumentException('Aucun scraper disponible pour cette URL.'),
        };

        return $scraper->scrape($url);
    }
}
