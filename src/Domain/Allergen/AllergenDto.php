<?php

namespace App\Domain\Allergen;

class AllergenDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $slug
    ) {
    }
}
