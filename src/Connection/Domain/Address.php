<?php

namespace App\Connection\Domain;

class Address
{
    public function __construct(
        public readonly string $address
    ) {
    }

    public function value(): string
    {
        return $this->address;
    }

    public function isEquals(Address $other): bool
    {
        return $this->address === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}