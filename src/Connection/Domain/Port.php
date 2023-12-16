<?php

namespace App\Connection\Domain;

class Port
{
    public function __construct(
        public readonly int $port
    ) {
    }

    public function value(): int
    {
        return $this->port;
    }

    public function isEquals(Port $other): bool
    {
        return $this->port === $other->value();
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}