<?php

namespace App\Domain\Admin\SeptCentCinquanteG;

class IngredientDto
{
    public string $singular;
    public string $plural;
    public ?string $quantity = null;
    public ?UnitDto $unit = null;
    public string $raw;
}
