<?php

namespace App\Http\Normalizer;

use App\Domain\Recipe\Recipe;
use App\Infra\Encoder\PathEncoder;
use App\Infra\Normalizer\Normalizer;

class RecipePathNormalizer extends Normalizer
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        if($object instanceof Recipe) {
            return [
                'path' => 'recipes_show',
                'params' => [
                    'slug' => $object->getSlug(),
                    'id' => $object->getId()
                ]
            ];
        }

        throw new \RuntimeException("Can't normalize path");
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return ($data instanceof Recipe) && PathEncoder::FORMAT === $format;
    }
}
