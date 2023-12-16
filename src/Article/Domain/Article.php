<?php
namespace App\Article\Domain;

final class Article
{
    public function __construct(
        private ArticleId $articleId,
        private Price $price,
        private Stock $stock,
        private Rate $rate,
        private TotalArticle $totalArticle,
    ) {
        $this->articleId = $articleId;
        $this->price = $price;
        $this->stock = $stock;
        $this->rate = $rate;
        $this->totalArticle = $totalArticle;
    }

    public function totalArticle(): int
    {
        return $this->totalArticle->value();
    }

    public function id(): string
    {
        return $this->articleId->value();
    }

    public function price(): float
    {
        return $this->price->value();
    }

    public function stock(): int
    {
        return $this->stock->value();
    }

    public function rate(): string
    {
        return $this->rate->value();
    }

    public function isEquals(Article $other): bool
    {
        return $this->id() === $other->id();
    }
}