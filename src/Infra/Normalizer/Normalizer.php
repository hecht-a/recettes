<?php

namespace App\Infra\Normalizer;

use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class Normalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed[] $context
     *
     * @return mixed[]
     */
    abstract public function normalize(mixed $object, string $format = null, array $context = []): array;

    /**
     * @param mixed[] $context
     */
    abstract public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool;

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
