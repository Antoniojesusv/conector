<?php
namespace App\Article\Domain;

final class ArticleLog
{
    public function __construct(
        private ArticleId $articleId,
        private Price $price,
        private Stock $stock,
        private Synchronized $synchronized,
        private TotalArticle $totalArticle
    ) {
        $this->articleId = $articleId;
        $this->price = $price;
        $this->stock = $stock;
        $this->totalArticle = $totalArticle;
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

    public function synchronized(): string
    {
        return $this->synchronized->value();
    }

    public function totalArticle(): int
    {
        return $this->totalArticle->value();
    }

    public function changeSynchronized(Synchronized $synchronized): void
    {
        if (!$this->synchronized->isEquals($synchronized)) {
            $this->synchronized = $synchronized;
        }
    }

    public function isEquals(ArticleLog $other): bool
    {
        return $this->id() === $other->id();
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id(),
            'price' => $this->price(),
            'stock' => $this->stock(),
            'synchronized' => $this->synchronized(),
            'totalArticle' => $this->totalArticle()
        ];
    }
}