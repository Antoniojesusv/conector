<?php

declare(strict_types=1);
namespace App\Article\Infrastructure\DataTransformer;

use App\Article\Application\Synchronization\FactoryMethod\ProductFactory;
use App\Article\Domain\Product;
use App\Article\Domain\ShopperGroups;

class ShopperGroupDataTransformer
{
    public function __construct(private ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }
    public function transform(Product $product, array $row): Product
    {
        $shopperGroups = [];

        if (!empty($row)) {
            $shopperGroups = $row;
        }

        $shopperGroups = new ShopperGroups($shopperGroups);
        $product->changeShopperGroups($shopperGroups);
        return $product;
    }
}