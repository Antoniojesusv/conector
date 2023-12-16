<?php

declare(strict_types=1);
namespace App\Article\Infrastructure\DataTransformer;

use App\Article\Application\Synchronization\FactoryMethod\ProductFactory;
use App\Article\Domain\Article;
use App\Article\Domain\Product;

class ProductRepositoryDataTransformer
{
    public function __construct(private ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }
    public function transform(Article $article, array $row): Product
    {
        $productId = '0';

        if (!empty($row)) {
            ['0' => $productId] = $row;
        }

        return $this->productFactory->create($article, (string) $productId);
    }
}