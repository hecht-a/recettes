<?php

namespace App\Infra\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class Normalizer implements NormalizerInterface
{
    #[\Override]
    abstract public function normalize(mixed $data, ?string $format = null, array $context = []): array;

    /**
     * @param mixed[] $context
     */
    #[\Override]
    abstract public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool;

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
