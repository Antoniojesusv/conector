<?php

namespace App\Connection\Domain;

class Status
{
    public function __construct(
        public readonly bool $status
    ) {
    }

    public function value(): bool
    {
        return $this->status;
    }

    public function isEquals(Status $other): bool
    {
        return $this->status === $other->value();
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}