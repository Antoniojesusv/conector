<?php

namespace App\Article\Domain;

class Synchronized
{
    public function __construct(
        private readonly string $synchronized
    ) {
    }

    public function value(): string
    {
        return $this->synchronized;
    }

    public function isEquals(Synchronized $synchronized): bool
    {
        return $this->synchronized === $synchronized->value();
    }

    public function __toString(): string
    {
        return (String) $this->value();
    }
}