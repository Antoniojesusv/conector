<?php

namespace App\Connection\Domain;

class Type
{
    public function __construct(
        public readonly string $type
    ) {
    }

    public function value(): string
    {
        return $this->type;
    }

    public function isEquals(Type $other): bool
    {
        return $this->type === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}