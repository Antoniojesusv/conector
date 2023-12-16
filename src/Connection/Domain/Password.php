<?php

namespace App\Connection\Domain;

class Password
{
    public function __construct(
        public readonly string $password
    ) {
    }

    public function value(): string
    {
        return $this->password;
    }

    public function isEquals(ConnectionId $other): bool
    {
        return $this->password === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}