<?php

declare(strict_types=1);
namespace App\Shop\Domain;

final class Shop
{
    public function __construct(
        private ShopId $shopId,
        private ShopName $name,
        private ShopRate $rate,
        private Store $store
    ) {
        $this->shopId = $shopId;
        $this->name = $name;
        $this->rate = $rate;
        $this->store = $store;
    }

    public function id(): ShopId
    {
        return $this->shopId;
    }

    public function name(): string
    {
        return $this->name->value();
    }

    public function rate(): string
    {
        return $this->rate->value();
    }

    public function store(): Store
    {
        return $this->store;
    }

    public function isEquals(Shop $other): bool
    {
        return $this->id() === $other->id();
    }
}
