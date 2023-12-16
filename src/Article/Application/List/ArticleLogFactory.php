<?php

declare(strict_types=1);
namespace App\Article\Application\List;

use App\Article\Domain\Article;
use App\Article\Domain\ArticleId;
use App\Article\Domain\ArticleLog;
use App\Article\Domain\Price;
use App\Article\Domain\Stock;
use App\Article\Domain\Synchronized;
use App\Article\Domain\TotalArticle;

class ArticleLogFactory
{
    public function create(
        Article $article,
        int $totalArticle,
        string $synchronized = 'No'
    ): ArticleLog {
        $articleIdValueObject = new ArticleId($article->id());
        $priceValueObject = new Price($article->price());
        $stockValueObject = new Stock($article->stock());
        $synchronizedValueObject = new Synchronized($synchronized);
        $totalArticle = new TotalArticle($totalArticle);

        return new ArticleLog(
            $articleIdValueObject,
            $priceValueObject,
            $stockValueObject,
            $synchronizedValueObject,
            $totalArticle
        );
    }
}