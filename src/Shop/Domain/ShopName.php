<?php

declare(strict_types=1);
namespace App\Shop\Domain;


final class ShopName
{
    public function __construct(
        private readonly string $name
    ) {
    }

    public function value(): string
    {
        return $this->name;
    }

    public function isEquals(ShopName $name): bool
    {
        return $this->name === $name->value();
    }

    public function __toString(): string
    {
        return (String) $this->value();
    }
}
