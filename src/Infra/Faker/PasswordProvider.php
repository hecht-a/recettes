<?php

namespace App\Infra\Faker;

use App\Domain\Auth\User;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PasswordProvider extends BaseProvider
{
    public function __construct(Generator $generator, private readonly UserPasswordHasherInterface $hasher)
    {
        parent::__construct($generator);
    }

    public function password(string $plainPassword): string
    {
        return $this->hasher->hashPassword(new User(), $plainPassword);
    }
}
