<?php

namespace App\Article\Domain;

class TotalArticle
{
    public function __construct(
        public readonly int $total
    ) {
    }

    public function value(): int
    {
        return $this->total;
    }

    public function isEquals(ArticleId $other): bool
    {
        return $this->total === $other->value();
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}