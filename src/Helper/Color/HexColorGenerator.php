<?php

namespace App\Helper\Color;

class HexColorGenerator
{
    public function generate(): string
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
}
