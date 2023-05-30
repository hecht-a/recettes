<?php

namespace App\Infra\Faker;

use Faker\Provider\Base as BaseProvider;

final class HexColorProvider extends BaseProvider
{
    public function password(string $plainPassword): string
    {
        return substr(uniqid(), -6);
    }
}
