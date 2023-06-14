<?php

namespace App\Http\Normalizer;

use App\Domain\Recipe\Recipe;
use App\Infra\Encoder\PathEncoder;
use App\Infra\Normalizer\Normalizer;

class RecipePathNormalizer extends Normalizer
{
    /**
     * @param mixed[] $context
     *
     * @return array{path: 'recipes_show', params: array{slug: string, id: ?int}}
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if ($object instanceof Recipe) {
            return [
                'path' => 'recipes_show',
                'params' => [
                    'slug' => $object->getSlug(),
                    'id' => $object->getId(),
                ],
            ];
        }

        throw new \RuntimeException("Can't normalize path");
    }

    /**
     * @param mixed[] $context
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return ($data instanceof Recipe) && PathEncoder::FORMAT === $format;
    }
}
