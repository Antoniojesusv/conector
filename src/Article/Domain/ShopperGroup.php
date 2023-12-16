<?php

namespace App\Article\Domain;

class ShopperGroup
{
    public function __construct(
        private ShopperGroupId $shopperGroupId,
    ) {
        $this->shopperGroupId = $shopperGroupId;
    }

    public function id(): string
    {
        return $this->shopperGroupId->value();
    }

    public function isEquals(ShopperGroup $shopperGroup): bool
    {
        return $this->shopperGroupId->value() === $shopperGroup->id();
    }
}