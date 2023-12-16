<?php

declare(strict_types=1);
namespace App\Article\Application\Synchronization\FactoryMethod;

use App\Article\Domain\Article;
use App\Article\Domain\ArticleId;
use App\Article\Domain\Price;
use App\Article\Domain\Rate;
use App\Article\Domain\Stock;
use App\Article\Domain\TotalArticle;

class ArticleFactory
{
    public function create(
        string $articleId,
        string $stock,
        string $price,
        string $rate,
        int $totalArticle
    ): Article {
        $articleIdValueObject = new ArticleId($articleId);
        $priceValueObject = new Price((float) $price);
        $stockValueObject = new Stock((int) $stock);
        $rateValueObject = new Rate($rate);
        $totalArticle = new TotalArticle($totalArticle);

        return new Article(
            $articleIdValueObject,
            $priceValueObject,
            $stockValueObject,
            $rateValueObject,
            $totalArticle
        );
    }
}