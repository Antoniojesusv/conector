<?php

namespace App\Article\Domain;

final class ShopperGroups
{
    public function __construct(
        private array $shopperGroups = []
    ) {
    }

    public function value(): array
    {
        return $this->shopperGroups;
    }

    public function isEquals(ShopperGroups $other): bool
    {
        $differences = array_diff($other->value(), $this->shopperGroups);
        return empty($differences);
    }

    public function __toString(): string
    {
        return json_encode($this->value());
    }
}