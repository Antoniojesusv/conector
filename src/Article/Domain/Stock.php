<?php

namespace App\Article\Domain;

class Stock
{
    public function __construct(
        private readonly int $final
    ) {
    }

    public function value(): int
    {
        return $this->final;
    }

    public function isEquals(Stock $stockNumber): bool
    {
        return $this->final === $stockNumber->value();
    }

    public function __toString(): string
    {
        return (String) $this->value();
    }
}