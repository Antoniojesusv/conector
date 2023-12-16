<?php

namespace App\Connection\Domain;

class DatabaseName
{
    public function __construct(
        public readonly string $name
    ) {
    }

    public function value(): string
    {
        return $this->name;
    }

    public function isEquals(DatabaseName $other): bool
    {
        return $this->name === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}