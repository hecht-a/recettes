<?php

namespace App\Infra\Encoder;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class PathEncoder implements EncoderInterface
{
    final public const FORMAT = 'path';

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * @param array $data
     */
    public function encode($data, string $format, array $context = []): string
    {
        ['path' => $path, 'params' => $params] = array_merge(['params' => []], $data);

        $url = $context['url'] ?? false;

        return $this->urlGenerator->generate($path, $params, $url ? UrlGeneratorInterface::ABSOLUTE_URL : UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding(string $format, array $context = []): bool
    {
        return self::FORMAT === $format;
    }
}
