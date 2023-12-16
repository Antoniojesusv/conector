<?php

namespace App\Article\Domain;

class ArticleId
{
    public function __construct(
        public readonly string $id
    ) {
    }

    public function value(): string
    {
        return $this->id;
    }

    public function isEquals(ArticleId $other): bool
    {
        return $this->id === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}