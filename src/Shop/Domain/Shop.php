<?php

declare(strict_types=1);
namespace App\Shop\Domain;

final class Shop
{
    public function __construct(
        private ShopId $shopId,
        private Rate $rate,
        private Store $store
    ) {
        $this->shopId = $shopId;
    }

    public function id(): ShopId
    {
        return $this->shopId;
    }

    public function Rate(): Rate
    {
        return $this->rate;
    }

    public function Store(): Store
    {
        return $this->store;
    }
}
