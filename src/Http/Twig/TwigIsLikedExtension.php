<?php

namespace App\Http\Twig;

use App\Domain\Auth\User;
use App\Domain\Recipe\Recipe;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigIsLikedExtension extends AbstractExtension
{
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_liked', $this->isLiked(...), ['is_safe' => ['html'], 'needs_context' => true]),
        ];
    }

    /**
     * @param array<string, mixed> $context
     */
    public function isLiked(array $context, Recipe $recipe): bool
    {
        /** @var User|null $user */
        $user = $context['app']->getUser();
        if (!$user) {
            return false;
        }

        return $user->getFavoriteRecipes()->contains($recipe);
    }
}
