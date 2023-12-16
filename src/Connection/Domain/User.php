<?php

namespace App\Connection\Domain;

class User
{
    public function __construct(
        public readonly string $user
    ) {
    }

    public function value(): string
    {
        return $this->user;
    }

    public function isEquals(User $other): bool
    {
        return $this->user === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}