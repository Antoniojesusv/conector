<?php

namespace App\Connection\Domain;

class ConnectionId
{
    public function __construct(
        public readonly string $id
    ) {
    }

    public function value(): string
    {
        return $this->id;
    }

    public function isEquals(ConnectionId $other): bool
    {
        return $this->id === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}