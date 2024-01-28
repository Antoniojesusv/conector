<?php

declare(strict_types=1);
namespace App\Shop\Domain;

final class ShopRate
{
    public function __construct(
        private readonly string $rate
    ) {
    }

    public function value(): string
    {
        return $this->rate;
    }

    public function isEquals(ShopRate $rate): bool
    {
        return $this->rate === $rate->value();
    }

    public function __toString(): string
    {
        return (String) $this->value();
    }
}
