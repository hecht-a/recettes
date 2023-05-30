<?php

namespace App\Http\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigFormatTimeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('format_time', $this->formatTime(...)),
        ];
    }

    public function formatTime(int $time): string
    {
        $minutes = $time % 60;
        $hour = ($time - $minutes) / 60;

        if ($hour > 0) {
            return "{$hour}h{$minutes}";
        }

        return "{$minutes} minutes";
    }
}
