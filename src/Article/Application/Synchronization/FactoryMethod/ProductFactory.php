<?php

declare(strict_types=1);
namespace App\Article\Application\Synchronization\FactoryMethod;

use App\Article\Domain\Article;
use App\Article\Domain\Product;
use App\Article\Domain\ProductId;
use App\Article\Domain\ShopperGroup;
use App\Article\Domain\ShopperGroupId;
use App\Article\Domain\ShopperGroups;

class ProductFactory
{
    public function create(Article $article, string $productId): Product
    {
        $productId = new ProductId($productId);
        $shopperGroupId = new ShopperGroupId($article->rate());
        $shopperGroup = new ShopperGroup($shopperGroupId);
        $shopperGroups = new ShopperGroups();

        return new Product(
            $productId,
            $shopperGroup,
            $shopperGroups
        );
    }
}