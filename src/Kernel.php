<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    #[\Override]
    public function boot(): void
    {
        parent::boot();

        $timezone = $this->getContainer()->getParameter('timezone');

        if (is_string($timezone)) {
            date_default_timezone_set($timezone);
        }
    }
}
