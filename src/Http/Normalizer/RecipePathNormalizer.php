<?php

namespace App\Http\Normalizer;

use App\Domain\Recipe\Recipe;
use App\Infra\Encoder\PathEncoder;
use App\Infra\Normalizer\Normalizer;

class RecipePathNormalizer extends Normalizer
{
    #[\Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if ($data instanceof Recipe) {
            return [
                'path' => 'recipes_show',
                'params' => [
                    'slug' => $data->getSlug(),
                    'id' => $data->getId(),
                ],
            ];
        }

        throw new \RuntimeException("Can't normalize path");
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return ($data instanceof Recipe) && PathEncoder::FORMAT === $format;
    }

    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            Recipe::class => true,
        ];
    }
}
