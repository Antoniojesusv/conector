<?php

namespace App\Article\Domain;

class Price
{
    public function __construct(
        private readonly float $pvp
    ) {
    }

    public function value(): float
    {
        return $this->pvp;
    }

    public function isEquals(Price $price): bool
    {
        return $this->pvp === $price->value();
    }

    public function __toString(): string
    {
        return (String) $this->value();
    }
}