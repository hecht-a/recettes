<?php

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigIsActiveExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_active', $this->isActive(...), ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function isActive(array $context, string $page): string
    {
        dump($context);
        if (($context['menuItem'] ?? null) === $page) {
            return ' aria-current="page"';
        }

        return '';
    }
}
