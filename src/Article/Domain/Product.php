<?php
namespace App\Article\Domain;

final class Product
{
    public function __construct(
        private ProductId $productId,
        private ShopperGroup $shopperGroup,
        private ShopperGroups $shopperGroups
    ) {
        $this->productId = $productId;
        $this->shopperGroup = $shopperGroup;
        $this->shopperGroups = $shopperGroups;
    }

    public function id(): string
    {
        return $this->productId->value();
    }

    public function shopperGroup(): ShopperGroup
    {
        return $this->shopperGroup;
    }

    public function shopperGroups(): ShopperGroups
    {
        return $this->shopperGroups;
    }

    public function changeShopperGroups(ShopperGroups $shopperGroups): void
    {
        if (!$this->shopperGroups->isEquals($shopperGroups)) {
            $this->shopperGroups = $shopperGroups;
        }
    }

    public function existShopperGroup(): bool
    {
        $result = in_array($this->shopperGroup->id(), $this->shopperGroups->value());
        return $result;
    }

    public function existProduct(): bool
    {
        return $this->productId->value() !== '0';
    }

    public function isEquals(Product $other): bool
    {
        return $this->id() === $other->id();
    }
}